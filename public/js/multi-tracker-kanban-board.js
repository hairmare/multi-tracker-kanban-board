dojo.provide("mtkb.app");

mtkb.app = {
    store: null,
    trackerUrl: dojo.config.trackerUrl,

    init: function() {
        console.log("initializing mtkb");
        this.startup();
    },

    startup: function() {
        this.initUi();
    },

    initUi: function() {
        // load project switch date and init switch
        this.initProjectSwitch();
    },

    initProjectSwitch: function() {
        store = new dojo.data.ItemFileReadStore({
            url: dojo.config.tracker_api + "projects.json"
        });
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
    },

    initBoard: function() {
        var states = new dojo.data.ItemFileReadStore({
            url: dojo.config.tracker_api + "states.json"
        });
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
        var head_tr = dojo.create('tr', {}, thead);

        dojo.forEach(states, function(state) {
            console.log("Create Column "+state.name);
            var th = dojo.create("th", {
                innerHTML: state.name
            }, head_tr);
        });
        board.startup();
    },

    loadTickets: function() {
        var tickets = new dojo.data.ItemFileReadStore({
            url: dojo.config.tracker_api + "tickets.json"
        });
        tickets.fetch({
            query: {}, 
            onBegin: function(size) {
                mtkb.app.ticketCount = size;
            },
            onComplete: function(data, store) {
                mtkb.app.showTicket(date, store)
            },
            start: 0
        });
    },
    showTicket: function(ticket, store) {
 
        var board = dijit.byId("boardTable");

        // event for portlets
        dropEvent = function(event, source, target) {
            mtkb.app.dropTask(event, this, source, target);
        };

                // prepare some Content for the Portlet:
                var portletContent1 = [
                dojo.create('div', {
                    innerHTML: 'Some content within the Portlet "dynPortlet1".'
                })];
                // create a new Portlet:
                var portlet1 = new dojox.widget.Portlet({
                    id: 'dynPortlet1',
                    closable: false,
                    dndType: 'Portlet',
                    title: 'Portlet "dynPortlet1"',
                    content: portletContent1
                });
                portlet1.watch(dropEvent);
                // add the first Portlet to the GridContainer:
        board.addChild(portlet1);

                var portletContent2 = [
                dojo.create('div', {
                    innerHTML: 'Some content within the Portlet "dynPortlet2".'
                })];
                var portlet2 = new dojox.widget.Portlet({
                    id: 'dynPortlet2',
                    closable: false,
                    dndType: 'Portlet',
                    title: 'Portlet "dynPortlet2"',
                    content: portletContent2
                });
        portlet2.watch(dropEvent);
        board.addChild(portlet2);
    },

    dropTask: function(event, widget, source, target) {
            if (event == "column") {
                console.log("Dragged from "+source+" to "+target+"!");
                console.log(widget)
            }
    }
}


