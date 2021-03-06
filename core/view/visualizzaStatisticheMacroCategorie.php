<!DOCTYPE html>
<html>
<head>

    <title>Crowdmine | Statistiche Macrocategorie</title>
    <?php include_once VIEW_DIR."headerStart.php";?>
</head>

<body>
<div class="app app-default">
    <div class="app-container no-sidebar">
        <?php include_once VIEW_DIR . "headerNavBar.php" ?>
        <div class="app-head"></div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">Statistiche Macro Categorie</div>
                    <div class="card-body">
                        <div>
                            <canvas id="statisticheMacro"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<?php include_once VIEW_DIR."footerStart.php";?>
<script src="<?php echo STYLE_DIR; ?> assets\js\Chart.min.js"></script>

<script type="text/javascript">

    $(document).ready(function () {
        $.ajax({
            type: "POST",
            url: "<?php echo DOMINIO_SITO;?>/macroCategorieStat",
            dataType: "json",
            data: {},
            success:function(response){
                drawMacroChart(response)
            }
        });
    });


    function drawMacroChart(result){

        var ctx = document.getElementById("statisticheMacro").getContext("2d");

        var numOfMacro = $.map(result, function(el,key){return key;}).length;

        var colors = rgbaRandom(numOfMacro);

        var macroChartData = {
            labels:[],
            datasets: createElements(result,colors)
        };

        var macroChart = new Chart.Bar(ctx, {
            data: macroChartData,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                pointHitRadius: 3,
                responsive: true,
                tooltipEvents: [],
                showTooltips: true,
                onAnimationComplete: function () {
                    this.showTooltip(this.segments, true);
                },
                tooltipTemplate: "<%= label %>  -  <%= value %>"
            }
        });
    }


    function rgbaRandom(numElements) {

        var rgbaArray = [
            "rgba(255,192,0.7)","rgba(255,192,0,0.7)","rgba(224,255,0,0.7)","rgba(126,255,0,0.7)"
            ,"rgba(33, 255,0,0.7)","rgba(0,255,65,0.7)","rgba(0,255,159,0.7)","rgba(0,253,255,0.7)"
            ,"rgba(0,159,255,0.7)","rgba(0,61,255,0.7)","rgba(33,0,255,0.7)","rgba(131,0,255,0.7)"
            ,"rgba(229,0,255,0.7)","rgba(0,82,255,0.7)","rgba(255,0,124,0.7)","rgba(16,0,255,0.7)"
        ];


        var resultSetColors = [];

        for (var k = 0; k < numElements; k++) {
            var numberRandom = Math.floor(Math.random() * (rgbaArray.length - 1) + 1);
                    resultSetColors.push(rgbaArray[numberRandom]);
                    rgbaArray.splice( $.inArray(rgbaArray[numberRandom],rgbaArray) ,1 );
        }

        return resultSetColors;
    }

    function createElements(result,colors) {
        var i=0;
        var allElement = [];
        $.map(result,function(item,key){
            var grafics = {
                label:key,
                data:[item],
                backgroundColor: colors[i],
                borderColor: colors[i],
                borderWidth: 1
            };
            allElement.push(grafics);
            i++;
        });
        return allElement;
    }
</script>

<?php include_once VIEW_DIR."footerEnd.php";?>

</html>