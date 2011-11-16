dojo.provide("mtkb.app");

mtkb.app = {

    init: function() {
        console.log("initializing mtkb");
        this.startup();
    },

    startup: function() {
        this.initStores();
        this.initUi();
    },

    initStores: function() {
        this.projectStore = new dojo.data.ItemFileReadStore({
            url             : dojo.config.tracker_api + "projects.json",
            clearOnClose    : true,
            urlPreventCache : true
        });
        this.stateStore = new dojo.data.ItemFileReadStore({
            url             : dojo.config.tracker_api + "states.json",
            clearOnClose    : true,
            urlPreventCache : true
        });
        this.ticketStore = new dojo.data.ItemFileReadStore({
            url             : dojo.config.tracker_api + "tickets.json",
            clearOnClose    : true,
            urlPreventCache : true
        });

    },

    initUi: function() {
        // load project switch date and init switch
        this.initProjectSwitch();
    },

    initProjectSwitch: function() {
        store = this.projectStore;
        projectSwitch = dijit.byId("projectSwitch");
        projectSwitch.setStore(store);
        projectSwitch.watch(function(eventType) {
            if (eventType == "value") {
                mtkb.app.doProjectSwitch()
            }
        });
    },

    doProjectSwitch: function() {
        projectSwitch = dijit.byId("projectSwitch");
        console.log("Switching Project to "+projectSwitch.value);

        this.clearBoard();
        this.initBoard();
    },

    clearBoard: function() {
        // @todo
    },

    initBoard: function() {
        var states = this.stateStore;
        states.fetch({
            query: {}, 
            onBegin: function(size) {
                mtkb.app.columnSize = size;
            },
            onComplete: function(data, store) {
                mtkb.app.initGrid(data, store);
                mtkb.app.loadTickets();
            },
            start: 0
        });
    },

    initGrid: function(states, store) {
        var board = dijit.byId("boardTable");
        board.setColumns(this.columnSize);

        var thead = dojo.create('thead', {}, board.gridContainerTable);
        var headTr = dojo.create('tr', {}, thead);

        dojo.forEach(states, function(state) {
            console.log("Create Column "+state.name);
            var th = dojo.create("th", {
                innerHTML: state.name
            }, headTr);
        });
        board.startup();
    },

    loadTickets: function() {
        var tickets = this.ticketStore;
        tickets.fetch({
            query: {
                type : "ticket"
            }, 
            onBegin: function(count) {
                mtkb.app.ticketCount = count;
            },
            onComplete: function(tickets, store) {
                mtkb.app.showTickets(tickets, store)
            },
            start: 0
        });
        tickets.fetch({
            query: {
                type : "task"
            }, 
            onBegin: function(count) {
                mtkb.app.taskCount = count;
            },
            onComplete: function(tickets, store) {
                mtkb.app.showTickets(tickets, store)
            },
            start: 0
        });
    },
    showTickets: function(tickets, store) {
        dojo.forEach(tickets, function(ticket) {
            mtkb.app.showTicket(ticket);
        });
    },

    showTicket: function(ticket) {
        var states = this.stateStore;
        var board  = dijit.byId("boardTable");

        // event for dnd stuff
        var dropEvent = function(event, source, target) {
            return mtkb.app.dropTask(event, this, source, target);
        };

        // contentPane containing data
        var ticketContent = new dijit.layout.ContentPane({
            content: [
                dojo.create('div', { innerHTML: ticket.description })
            ]
        });

        // nice looking widget based on the data
        var ticketPortlet = new dojox.widget.Portlet({
            id       : "ticket_"+ticket.id,
            closable : false,
            title    : ticket.name,
            dndType  : 'Ticket',
            content  : ticketContent
        });

        // wrap that up
        ticketPortlet.watch(dropEvent);
        board.addChild(ticketPortlet);
    },

    dropTask: function(event, widget, source, target) {
            if (event == "column") {
                console.log("Dragged from "+source+" to "+target+"!");
                console.log(widget)
            }
    }
}


