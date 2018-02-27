<?php
    foreach($graphData['datasets'] as $key => $dataset) {
        $dataSetId = 1;

        foreach($dataset as $key => $set) {				
            if($dataSetId == 1) {	
                foreach($set['data'] as $data) {
                    array_push($labelsArray, html_entity_decode($data['label']));
                }
            }

            $set['label'] = str_replace("&#39;", "\'", $set['label']);

            $datasetHtml .= "{
                label: '" . html_entity_decode($set['label']) . "',
                data: [" . returnGraphData($set['data']) . "],
                pointBackgroundColor: [" . returnGraphBackgroundColours($set['data']) . "],
                borderWidth: 2, 
                pointRadius: 5,
                borderColor: '{$set['line_color']}',
            },";

            $dataSetId++;
        }
    }

    // START OF CHART
    $view .= "
    <script>
    var ctx = document.getElementById('graph-{$chartId}').getContext('2d');
    var myChart = new Chart(ctx, {
        type: '{$graphData['graphType']}',
        data: {
            labels: [" . returnGraphLabels($labelsArray) . "],
            datasets: [{$datasetHtml}],
            fill: false,
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: '" . html_entity_decode(get_the_title($chartId)) . "',
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }],
            },
            legend: {
                display: true,
                position: 'bottom',
            },
        }
    });
    </script>";