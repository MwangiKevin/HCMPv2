<?php
//echo"<pre>";print_r($stock_status);die;
include_once 'ago_time.php';

$reporting_percentage = $cumulative_result/$total_facilities*100;
$reporting_percentage = number_format($reporting_percentage, $decimals = 0);
?>
<style type="text/css">
    #inner_wrapper{font-size: 80%;}
    .tab-pane{padding-left: 6px;}
    #tab1 > ul > li > ul{font-size: 11px;}
    #tab1 > ul > li.span4{background: rgba(204, 204, 204, 0.14);padding: 13px;border: solid 1px #ccc;color: #92A8B4; height: 300px;overflow-y: scroll;}
    #chartdiv {width: 100%;height    : 500px;font-size : 11px;} 
    #stock_table{width: 100%;}
</style>
<div class="tabbable">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Activity</a></li>
        <li><a href="#StockStatus" data-toggle="tab">Stock Status</a></li>
        <li><a href="#CountyProgess" data-toggle="tab">Counties Progress</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <ul class="thumbnails">
                <li class="span11">
                    <div id="container" style="min-width: 310px; height: 360px; margin: 0 auto"></div>
                </li>
                <li class="span4">
                    <ul class="unstyled">
                        <?php
                        foreach ($user_logs as $logs) {
                            $action_clause = '';
                            $action_clause = ($logs['action'] == 'ADDFDR') ? 'Added a Report' : $action_clause;
                            $action_clause = ($logs['action'] == 'UPDFDR') ? 'Edited a Report' : $action_clause;
                            $action_clause = ($logs['action'] == 'ACTFCL') ? 'Activated a Facility' : $action_clause;
                            $action_clause = ($logs['action'] == 'DCTFCL') ? 'Deactivated a Facility' : $action_clause;
                            $action_clause = ($logs['action'] == 'UPDFCL') ? 'Edited a Facility' : $action_clause;
                            $action_clause = ($logs['action'] == 'ADDUSR') ? 'Added  a User' : $action_clause;
                            $action_clause = ($logs['action'] == 'UPDUSR') ? 'Edited a User' : $action_clause;
                            $link = "";
                            $date = date('H:m:s d F, Y', $logs['timestamp']);
                            ?>
                            <li>
                                <?php
                                echo
                                '<a href="' . $link . '" title="' . $date . '">'
                                . $logs['fname'] . ' '
                                . $logs['lname'] . ' '
                                . '</a>'
                                . $action_clause . ' '
                                . '<a href="' . $link . '" title="' . $date . '">'
                                . timeAgo($logs['timestamp'])
                                . '</a>';
                                ?>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            Countrywide Progress: <?php echo $reporting_percentage; ?>%
            <div class="progress progress-info" style="height: 10px;">
                <div class="bar" style="width: <?php echo $reporting_percentage; ?>%"></div>
            </div>
        </div>
        <div class="tab-pane" id="StockStatus">
            <table id="stock_table">
                <thead>
                <th>County</th>
                <th>Commodity</th>
                <th>Beginning Balance</th>
                <th>Received Qty</th>
                <th>Used Qty</th>
                <th>Tests Done</th>
                <th>Closing Balance</th>
                <th>Requested Qty</th>
                <th>Out of Stock days</th>
                <th>Expiring Qty</th>
                <th>Allocated Qty</th>
                </thead>
                <tbody>
                    <?php 
                    $count = count($stock_status);
                    for ($i=0; $i<$count; $i++){
                    foreach ($stock_status[$i] as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value['county']; ?></td>
                            <td><?php echo $value['commodity_name']; ?></td>
                            <td><?php echo $value['sum_opening']; ?></td>
                            <td><?php echo $value['sum_received']; ?></td>
                            <td><?php echo $value['sum_used']; ?></td>
                            <td><?php echo $value['sum_tests']; ?></td>
                            <td><?php echo $value['sum_closing_bal']; ?></td>
                            <td><?php echo $value['sum_requested']; ?></td>
                            <td><?php echo $value['sum_days']; ?></td>
                            <td><?php echo $value['sum_expiring']; ?></td>
                            <td><?php echo $value['sum_allocated']; ?></td>
                        </tr>
                    <?php } }?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="CountyProgess">
            <p>Howdy, I'm in Section 2.</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $("table").tablecloth({theme: "paper"});
        var table = $('#stock_table').dataTable({
            "sDom": "T lfrtip",
            "bPaginate": false,
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
            },
            "oTableTools": {
                "aButtons": [
                    "copy",
                    "print",
                    {
                        "sExtends": "collection",
                        "sButtonText": 'Save',
                        "aButtons": ["csv", "xls", "pdf"]
                    }
                ],
                "sSwfPath": "../v2/assets/datatable/media/swf/copy_csv_xls_pdf.swf"
            }
        });

        $('#container').highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: 'Reporting Trends <?php echo $englishdate ?>'
            },
            subtitle: {
                text: 'Live Data from reports'
            },
            xAxis: {
                categories: <?php echo $jsony; ?>
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'F-CDRR Reports'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.0f} Reports</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            credits: false,
            series: [{
                    name: 'Culmulative Trend',
                    data: <?php echo $jsonx1; ?>
                },
                {
                    name: 'Trend',
                    data: <?php echo $jsonx; ?>
                }]
        });
    });
</script>

<link rel="stylesheet" type="text/css" href="http://tableclothjs.com/assets/css/tablecloth.css">
<script src="http://tableclothjs.com/assets/js/jquery.tablesorter.js"></script>
<script src="http://tableclothjs.com/assets/js/jquery.metadata.js"></script>
<script src="http://tableclothjs.com/assets/js/jquery.tablecloth.js"></script>

<!--Datatables==========================  --> 
<script src="http://cdn.datatables.net/1.10.0/js/jquery.dataTables.js" type="text/javascript"></script>
<script src="../v2/assets/datatable/jquery.dataTables.min.js" type="text/javascript"></script>  
<script src="../v2/assets/datatable/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="../v2/assets/datatable/TableTools.js" type="text/javascript"></script>
<script src="../v2/assets/datatable/ZeroClipboard.js" type="text/javascript"></script>
<script src="../v2/assets/datatable/dataTables.bootstrapPagination.js" type="text/javascript"></script>
<!-- validation ===================== -->

<link href="../v2/assets/datatable/TableTools.css" type="text/css" rel="stylesheet"/>
<link href="../v2/assets/datatable/dataTables.bootstrap.css" type="text/css" rel="stylesheet"/>