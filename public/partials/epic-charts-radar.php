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
            
            $color = reset($set['data']);
            $color = implode(",", hex2RGB($color['background_color']));								

            $backgroundColor = "rgba(" . $color . ", 0.6)";

            $datasetHtml .= "{
                label: '" . html_entity_decode($set['label']) . "',
                data: [" . returnGraphData($set['data']) . "],
                fill: true,
                backgroundColor: ['" . $backgroundColor . "'],
                pointBackgroundColor: [" . returnPointColours($set['data']) . "],
                borderWidth: 6, 
                pointRadius: 6,
            }," . PHP_EOL;

            $dataSetId++;
        }
    }

    // START OF CHART
    $view .= "
    <script>
    var ctx = document.getElementById('graph-{$chartId}').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: [" . returnGraphLabels($labelsArray) . "],
            datasets: [{$datasetHtml}],
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: '" . html_entity_decode(get_the_title($chartId)) . "',
            },
            scale: {
                    ticks: {
                        beginAtZero:true
                    }
                },
            }
    });
    </script>";