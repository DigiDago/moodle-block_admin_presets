M.block_admin_presets = {

    tree: null,
    nodes: null,


    /**
     * Initializes the TreeView object and adds the submit listener
     */
    init: function(Y) {

        Y.use('yui2-treeview', function(Y) {

            var context = M.block_admin_presets;

            context.tree = new Y.YUI2.widget.TreeView("settings_tree_div");

            context.nodes = new Array();
            context.nodes.root = context.tree.getRoot();
        });
    },

    /**
     * Creates a tree branch
     */
    addNodes: function(Y, ids, nodeids, labels, descriptions, parents) {

        var context = M.block_admin_presets;

        var nelements = ids.length;
        for (var i = 0; i < nelements; i++) {

            var settingId = ids[i];
            var nodeId = nodeids[i];
            var label = decodeURIComponent(labels[i]);
            var description = decodeURIComponent(descriptions[i]);
            var parent = parents[i];

            var newNode = new Y.YUI2.widget.HTMLNode(label, context.nodes[parent]);

            newNode.settingId = settingId;
            newNode.setNodesProperty('title', description);
            newNode.highlightState = 1;

            context.nodes[nodeId] = newNode;
        }
    },

    render: function(Y) {

        var context = M.block_admin_presets;

        // Cleaning categories without children
        if (categories = context.tree.getNodesByProperty('settingId', 'category')) {
            for (var i = 0; i < categories.length; i++) {
                if (!categories[i].hasChildren()) {
                    context.tree.popNode(categories[i]);
                }
            }
        }

        if (categories = context.tree.getRoot().children) {
            for (var i = 0; i < categories.length; i++) {
                if (!categories[i].hasChildren()) {
                    context.tree.popNode(categories[i]);
                }
            }
        }

        // Context.tree.expandAll();
        context.tree.setNodesProperty('propagateHighlightUp', true);
        context.tree.setNodesProperty('propagateHighlightDown', true);
        context.tree.subscribe('clickEvent', context.tree.onEventToggleHighlight);
        context.tree.render();

        // Listener to create one node for each selected setting
        Y.YUI2.util.Event.on('id_admin_presets_submit', 'click', function() {

            // We need the moodle form to add the checked settings
            var settingsPresetsForm = document.getElementById('id_admin_presets_submit').parentNode;

            var hiLit = context.tree.getNodesByProperty('highlightState', 1);
            if (Y.YUI2.lang.isNull(hiLit)) {
                Y.YUI2.log("Nothing selected");

            } else {

                // Only for debugging
                var labels = [];

                for (var i = 0; i < hiLit.length; i++) {

                    treeNode = hiLit[i];

                    // Only settings not setting categories nor settings pages
                    if (treeNode.settingId != 'category' && treeNode.settingId != 'page') {
                        labels.push(treeNode.settingId);

                        // If the node does not exists we add it
                        if (!document.getElementById(treeNode.settingId)) {

                            var settingInput = document.createElement('input');
                            settingInput.setAttribute('type', 'hidden');
                            settingInput.setAttribute('name', treeNode.settingId);
                            settingInput.setAttribute('value', '1');
                            settingsPresetsForm.appendChild(settingInput);
                        }
                    }
                }

                Y.YUI2.log("Checked settings:\n" + labels.join("\n"), "info");
            }
        });
    }
};
