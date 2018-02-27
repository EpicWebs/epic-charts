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
                ";

                switch($graphData['graphType']) {
                    case "line":
                        $datasetHtml .= "pointBackgroundColor: [" . returnGraphBackgroundColours($set['data']) . "],";
                        break;
                    case "radar":
                        $datasetHtml .= "fill: true,
                        ";

                        $backgroundColor = reset($set['data']);
                        $backgroundColor = implode(",", hex2RGB($backgroundColor['background_color']));								

                        $backgroundColor = "rgba(" . $backgroundColor . ", 0.6)";

                        $datasetHtml .= "backgroundColor: ['" . $backgroundColor . "'],";
                        break;
                    default;
                        $datasetHtml .= "backgroundColor: [" . returnGraphBackgroundColours($set['data']) . "],";
                        break;
                }

            // Additional dataset properties
            $datasetHtml .= "
                borderWidth: 2, 
                pointRadius: 5,
            ";

            if($graphData['graphType'] == "line") {
                $datasetHtml .= "
                    borderColor: '{$set['line_color']}',
                ";
            }

            $datasetHtml .= "},";

            $dataSetId++;
        }
    }

    // START OF CHART
    $view .= "
    <script>
    var ctx = document.getElementById('graph-{$chartId}').getContext('2d');
    var myChart = new Chart(ctx, {
        type: '{$graphData['graphType']}',
        data: {";

        $view .= "labels: [" . returnGraphLabels($labelsArray) . "],";

        $view .= "datasets: [{$datasetHtml}],";

    // CHART TYPE OPTIONS
    if($graphData['graphType'] == "line") {
        $view .= "fill: false,
        ";
    }

    // END OF CHART
        $view .= "
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
            },";

        if($graphData['graphType'] == "radar") {
            $view .= "scale: {
                    ticks: {
                        beginAtZero:true
                    }
                },";
        }

        if($graphData['graphType'] != "radar") {
            $view .= "
                legend: {
                    display: true,
                    position: 'bottom',
                },";
        }

        $view .= "}
    });
    </script>";