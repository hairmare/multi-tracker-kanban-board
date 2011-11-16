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
        states.fetch({query: {}, onBegin: this.initGrid, start: 0, count: 0});
    },

    initGrid: function(size, req) {
        var board = dijit.byId("boardTable");
        board.setColumns(size);

        /**
        var states = new dojo.data.ItemFileReadStore({
            url: dojo.config.tracker_api + "states.json"
        });
        states.query({}),forEach(function(state) {
            console.log(state);
        });*/

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
        board.addChild(portlet2);
        board.startup();
    },

    dropTask: function(source, nodes, copy, target) {
        // Your Code here
            console.log(source);
            console.log(target);
    }
}


