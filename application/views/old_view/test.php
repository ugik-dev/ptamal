<html>

<head>
    <title>jstree treetable API test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" rel="stylesheet">
    <script type='text/javascript' src='http://code.jquery.com/jquery-2.1.0.js'></script>
    <script type="text/javascript" src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/dist/js/jstreetable.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var data = [{
                    "id": "Root",
                    "parent": "#",
                    "text": "Root"
                },
                {
                    "id": "child1",
                    "parent": "Root",
                    "text": "child1",
                    "data": {
                        "open": "2/5/2015 2:18:00 PM",
                        "close": "2/6/2015 10:16:00 AM",
                        "status": "Closed"
                    }
                },
                {
                    "id": "child1-1",
                    "parent": "child1",
                    "text": "child1-1",
                    "data": {
                        "open": "2/5/2015 2:18:00 PM",
                        "close": "2/6/2015 10:16:00 AM",
                        "status": "Closed"
                    }
                },
                {
                    "id": "child2-2",
                    "parent": "child1",
                    "text": "child2-2",
                    "data": {
                        "open": "2/5/2015 2:18:00 PM",
                        "close": "2/6/2015 10:16:00 AM",
                        "status": "Closed"
                    }
                },
                {
                    "id": "child2",
                    "parent": "Root",
                    "text": "child2",
                    "data": {
                        "open": "2/5/2015 2:18:00 PM",
                        "close": "2/6/2015 10:16:00 AM",
                        "status": "Closed"
                    }
                },
                {
                    "id": "child3",
                    "parent": "Root",
                    "text": "child3",
                    "data": {
                        "open": "2/5/2015 2:18:00 PM",
                        "close": "2/6/2015 10:16:00 AM",
                        "status": "Closed"
                    }
                }
            ]


            $('#jstree').jstree({
                "core": {
                    "data": data
                },
                "plugins": ["table"],
                "search": {
                    "show_only_matches": false
                },
                "table": {
                    columns: [{
                            width: 190,
                            header: "name"
                        },
                        {
                            width: 140,
                            header: "open",
                            value: "open"
                        },
                        {
                            width: 140,
                            header: "close",
                            value: "close"
                        },
                        {
                            width: 130,
                            header: "status",
                            value: "status"
                        }
                    ]
                }
            });

            var to = false;
            $('#hideCol').click(function() {
                var ref = $("#jstree").jstree(true)
                ref.table_hide_column(1);
            });
            $('#showCol').click(function() {
                var ref = $("#jstree").jstree(true)
                ref.table_show_column(1);
            });
        });
    </script>
</head>

<body>
    <h2>API Test</h2>
    <button id="hideCol">Hide Col 1</button>&nbsp;&nbsp;<button id="showCol">Show Col 1</button>

    <div id="jstree"></div>
</body>

</html>