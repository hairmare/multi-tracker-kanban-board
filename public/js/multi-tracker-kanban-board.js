dojo.provide("mtkb.app");

mtkb.app = {

    init: function() {
        this.startup();
    },

    startup: function() {
        this.initRpc();
        this.initUi();
    },

    initRpc: function() {
        this.rpc = new dojo.rpc.JsonService(dojo.config.trackerApi);
    },

    initUi: function() {
        // load project switch data and init switch
        this.rpc.projects().addCallback(function(projects) {
            mtkb.app.projectStore = projects;
            mtkb.app.initProjectSwitch();
        });
    },

    initProjectSwitch: function() {
        store = new dojo.data.ItemFileReadStore({
            data : this.projectStore
        });
        projectSwitch = dijit.byId("projectSwitch");
        projectSwitch.setStore(store);
        projectSwitch.watch(function(eventType) {
            if (eventType == "value") {
                mtkb.app.doProjectSwitch()
            }
        });
        // call switch to initialize the board after loading
        this.doProjectSwitch();
    },

    doProjectSwitch: function() {
        projectSwitch = dijit.byId("projectSwitch");
        console.info("Switching project to #"+projectSwitch.value);

        this.clearBoard();
        this.rpc.states().addCallback(function(states) {
            mtkb.app.stateStore = states;
            mtkb.app.initBoard();
        });
    },

    clearBoard: function() {
        dijit.byId("boardTable").destroyDescendants()
    },

    initBoard: function() {
        var states = this.stateStore.items;
        this.columnSize = states.length
        this.initGrid(states)
        this.loadTickets();
    },

    initGrid: function(states) {
        var board = dijit.byId("boardTable");
        board.setColumns(this.columnSize);

        if (!this._gridHeadCreated) {
            var thead = dojo.create('thead', {}, board.gridContainerTable);
            var headTr = dojo.create('tr', {
                class : "dijitTitlePaneTitle"
            }, thead);

            dojo.forEach(states, function(state) {
                var th = dojo.create("th", {
                    innerHTML: state.name
                }, headTr);
            });
            console.info("created "+states.length+" columns");
            console.groupCollapsed();
            console.table(states);
            console.groupEnd();
            this._gridHeadCreated = true;
        }
        board.startup();
    },

    loadTickets: function() {
        console.groupCollapsed("getting project tickets");
        console.info("loading tickets")
        this.rpc.tickets(
            dijit.byId("projectSwitch").value
        ).addCallback(function(tickets) {
            mtkb.app.showTickets(tickets)
        });
    },
    showTickets: function(tickets) {
        console.info("got "+tickets.items.length+" tickets");
        dojo.forEach(tickets.items, function(ticket) {
            mtkb.app.showTicket(ticket);
        });
        console.info("added tickets to board");
        console.table(tickets.items);
        console.groupEnd();
    },

    showTicket: function(ticket, dndType) {
        if (typeof dndType == "undefined") {
            dndType = "Ticket"
        }
        var board  = dijit.byId("boardTable");

        // event for dnd stuff
        var dropEvent = function(event, source, target) {
            return mtkb.app.dropTicket(event, this, source, target);
        };

        // contentPane containing data
        var ticketContent = new dijit.layout.BorderContainer({
            class : "ticketContentContainer"
        });
        ticketContent.addChild(
            new dijit.layout.ContentPane({
                content: ticket.description,
                region: "center"
            })
        );

        // find right column
        colNum = this._findColumn(ticket)

        // nice looking widget based on the data
        var ticketPortlet = new dojox.widget.Portlet({
            id       : "ticket_"+ticket.id,
            closable : false,
            toggleable : true,
            title    : ticket.name,
            column   : colNum,
            dndType  : dndType,
            content  : ticketContent,
            data     : ticket
        });

        // wrap that up
        ticketPortlet.watch(dropEvent);
        board.addChild(ticketPortlet, colNum);
        // resize to force redraw
        ticketPortlet.resize();
        return ticketPortlet;
    },

    dropTicket: function(event, widget, source, target) {
        if (event == "column") {
            mtkb.app._dragTicket = widget;
            mtkb.app._dragTarget = target;
            dijit.byId("updateTicketDialog").show();
        }
    },

    abortDragTicket: function() {
        var board = dijit.byId("boardTable");

        var data = this._dragTicket.data;
        this._dragTicket.destroy();
        this.showTicket(data);
    },

    updateTicket: function(form) {
        var data = this._dragTicket.data;
        var status = this.stateStore.items[this._dragTarget];

        // update resident data
        data.status = status;

        // react to user asap
        dijit.byId("updateTicketDialog").hide();
        this._dragTicket.destroy();
        this._dragTicket = this.showTicket(data, "Ticket_NoDrag");

        // then do the save
        this.rpc.moveTicket(
            data.id,
            status.id,
            dijit.byId('commit-note').get("value") // timing errors are why i grab it this way and not thru form.value.note
        ).addCallback(function(issue) {
            console.info("Ticket stored on server");
            // reload stuff
            var board = dijit.byId("boardTable");
            mtkb.app._dragTicket.destroy();
            mtkb.app.showTicket(issue.items[0]);
        });
    },

    _findColumn: function(ticket) {
        colNum = 0;
        foundColNum = false;
        dojo.forEach(this.stateStore.items, function(state) {
            if (state.id == ticket.status.id) {
                foundColNum = true;
            }
            if (!foundColNum) {
                colNum++;
            }
        });
        return colNum;
    },

}


