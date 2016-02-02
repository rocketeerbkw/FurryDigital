<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        <!--
        body {
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            background-color: #FFFFFF;
        }
        body, td, th, div {
            font-family: "Tahoma", Verdana, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }
        h1 {
            font-size: 20px;
            font-weight: normal;
        }
        h2 {
            font-size: 17px;
            font-weight: bold;
        }
        h3 {
            font-size: 14px;
            font-weight: bold;
        }

        small {
            font-size: 11px;
            color: #999999;
        }

        dt {
            font-weight: bold;
        }

        #content-wrapper {
            padding: 5px;
            background-color: #FFFFFF;
        }
        #content {
            border-top: 1px solid #CCC;
            border-bottom: 1px solid #CCC;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        #footer {
            font-size: 11px;
            color: #999999;
        }

        table.datatable {
            border: 1px solid #CCC;
            border-collapse: collapse;
        }

        table.datatable th {
            background: #EEE;
            border: 1px solid #CCC;
            padding: 6px 4px;
        }
        table.datatable tfoot tr td {
            background: #EEE;
            border-left: 1px solid #CCC;
            border-top: 1px solid #CCC;
            padding: 6px 4px;
        }

        table.datatable tr td {
            background: #EEE;
            border-left: 1px solid #CCC;
            border-bottom: 1px solid #EEE;
            padding: 4px;
            vertical-align: top;
        }
        table.datatable tr.even td {
            background: #EDF5FF;
        }
        table.datatable tr.odd td {
            background: #FFF;
        }

        table tr td.left_padded,
        table tr th.left_padded {
            padding-left: 10px;
        }
        -->
    </style>
</head>
<body>
<div style="padding: 5px;">
    <h1>{{ config.application.name }}</h1>

    {{ content() }}
</div>
</body>
</html>