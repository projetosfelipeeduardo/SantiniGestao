@extends('adminlte::master')





<div class="container">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-4">
            <div class="text-center mt-5">
                <img style="width:100%;" height="80" src="{{URL::asset('images/brick.jpg')}}" alt="">
            </div>
            <div class="text-center">
                <span id="city">OURINHOS/SP</span>
            </div>
            <div class="mt-5">
                <h3 style="letter-spacing: 2px; font-weight:600; color:gold;">PROMOÇÃO</h3>
            </div>
            <div>
                <h2 style="color:blue">Sera que hoje é</h2>
                <h2 style="color:gold;">&ensp;seu dia de sorte? </h2>
            </div>
            <div class="mt-4" style="font-size: 12px;text-align: justify;">
                <p>It is a long established fact that a reader will be distracted by the readable content of a page when
                    looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
                    distribution of letters, as opposed to using 'Content here, content here', making it look like
                    readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their
                    default model text, and a search for 'lorem ipsum' will uncover many web sites still in their
                    infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose
                    (injected humour and the like).</p>
            </div>
            <div>
                <div class="input-group">
                    <input class="form-control" name="nome" aria-label="With textarea" />
                    <label for="nome" class="input-group-prepend">
                        <span  class="input-group-text label">Nome*</span>
                    </label>
                </div>

                <div class="input-group">
                    <input class="form-control" name="cpf" aria-label="With textarea" />
                    <label for="cpf" class="input-group-prepend">
                        <span class="input-group-text label">CPF*</span>
                    </label>
                </div>

                <div class="input-group">
                    <input class="form-control" name="telefone" aria-label="With textarea" />
                    <label for="nome" class="input-group-prepend">
                        <span class="input-group-text label">Telefone*</span>
                    </label>
                </div>

                <div class="input-group">
                    <input class="form-control" name="email" aria-label="With textarea" />
                    <label for="nome" class="input-group-prepend">
                        <span class="input-group-text label">Email</span>
                    </label>
                </div>


            </div>

            <div class="mt-4 text-center">
                <input  type="button" value="Aperte Aqui Para Girar a Roleta" class="btn btn-lg btn-primary text-light" style="font-size: 18pt" id='spin' />
            </div>

            <div class="mt-4 text-center">
                <canvas id="chartProgress" width="300px" height="200"></canvas>
            </div>
            <div class="text-center">
                <strong>Seja Rápido</strong>
                <p>50% dos descontos já foram entregues</p>
            </div>
        </div>
        <div class="col-lg-1">

        </div>




        <div  style="background-image: url({{URL::asset('images/teste.jpg')}})" class="col-lg-6 coluna-2">
            <div style="background-color: rgba(255,255,255, 0.5); height:100%;" class="text-center">
                <canvas width="500" height="500" id="canvas"></canvas>
            </div>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js"></script>


<style>

#canvas{
    top: 60%;
    position: relative;
    width: 400px;
    height: 400px;
    background-repeat: no-repeat;
    background-image: url({{URL::asset('images/circulo.png')}});
    background-size: 100% 100%;


}

@media (max-width: 600px) {
        .navbar-nav {
            margin: 7.5px 0px;
        }


    #canvas {
        position: relative;
        width:300px;
        height:300px;
        background-size: 100% 100%;
    }
}
    body {
        background: white !important;
        /* fallback for old browsers */

    }

    .container {
        background: #eee !important;
        overflow: hidden !important;
    }

    h2,
    h1 {
        font-size: 26px !important;
        font-family: 'Lobster', cursive !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
        text-rendering: optimizeLegibility !important;
        text-shadow: rgba(0, 0, 0, .01) 0 0 1px;
    }

    h1,
    h2,
    h3 {

        line-height: 0.8 !important;
    }

    h3 {
        font-size: 16px !important;
    }

    #city {
        color: royalblue;
    }


    .coluna-2 {
        background-color: lightblue !important;
        padding: 0 !important;
    }

.label{
    border-radius: 0 10px 10px 0!important;
    background-color: lightblue!important;
    color: #222!important;
}
</style>

<script>
    var chartProgress = document.getElementById("chartProgress");
if (chartProgress) {
  var myChartCircle = new Chart(chartProgress, {
    type: 'doughnut',
    data: {
      labels: ["Africa", 'null'],
      datasets: [{
        label: "Population (millions)",
        backgroundColor: ["#5283ff"],
        data: [70, 30]
      }]
    },
    plugins: [{
      beforeDraw: function(chart) {
        var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;

        ctx.restore();
        var fontSize = (height / 150).toFixed(2);
        ctx.font = fontSize + "em sans-serif";
        ctx.fillStyle = "#9b9b9b";
        ctx.textBaseline = "middle";

        var text = "50%",
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;

        ctx.fillText(text, textX, textY);
        ctx.save();
      }
  }],
    options: {
      legend: {
        display: false,
      },
      responsive: true,
      maintainAspectRatio: false,
      cutoutPercentage: 85
    }

  });


}
</script>


<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>

var options = [];
var colors = [];
var contador = 0;

@foreach($roleta as $r)
@if($r->status == 1)
options[contador] = '<?= $r->item ?>';
colors[contador] = '<?= $r->cor ?>';
contador++;
@endif
@endforeach;




var startAngle = 0;
var arc = Math.PI / (options.length / 2);
var spinTimeout = null;

var spinArcStart = 10;
var spinTime = 0;
var spinTimeTotal = 0;

var ctx;

document.getElementById("spin").addEventListener("click", spin);

function byte2Hex(n) {
  var nybHexString = "0123456789ABCDEF";
  return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
}

function RGB2Color(r,g,b) {
	return '#' + byte2Hex(r) + byte2Hex(g) + byte2Hex(b);
}



function drawRouletteWheel() {
  var canvas = document.getElementById("canvas");
  if (canvas.getContext) {
    var outsideRadius = 200;
    var textRadius = 130;
    var insideRadius = 60;

    ctx = canvas.getContext("2d");
    ctx.clearRect(0,0,375,375);

    ctx.strokeStyle = "#eee";
    ctx.lineWidth = -1;

    ctx.font = 'bolder 20px Helvetica, Arial';

    for(var i = 0; i < options.length; i++) {
      var angle = startAngle + i * arc;
      //ctx.fillStyle = colors[i];
      ctx.fillStyle = colors[i];

      ctx.beginPath();
      ctx.arc(250, 250, outsideRadius, angle, angle + arc, false);
      ctx.arc(250, 250, insideRadius, angle + arc, angle, true);
      ctx.stroke();
      ctx.fill();

      ctx.save();
      ctx.shadowOffsetX = -1;
      ctx.shadowOffsetY = -1;
      ctx.shadowBlur    = 0;
      ctx.shadowColor   = "black";
      ctx.fillStyle = "white";
      ctx.translate(250 + Math.cos(angle + arc / 2) * textRadius,
                    250 + Math.sin(angle + arc / 2) * textRadius);
      ctx.rotate(angle + arc / 2 + Math.PI / 2);
      var text = options[i];
      ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
      ctx.restore();
    }

    //Arrow
    ctx.fillStyle = "gold";
    ctx.beginPath();
    ctx.shadowOffsetX = -1;
    ctx.shadowOffsetY = -1;
    ctx.shadowBlur    = 0;
    ctx.shadowColor   = "black";
    ctx.moveTo(250 - 10, 250 - (outsideRadius + 7));
    ctx.lineTo(250 + 10, 250 - (outsideRadius + 7));
    ctx.lineTo(250 + 10, 250 - (outsideRadius - 7));
    ctx.lineTo(250 + 15, 250 - (outsideRadius - 7));
    ctx.lineTo(250 + 2, 250 - (outsideRadius - 15));
    ctx.lineTo(250 - 15, 250 - (outsideRadius - 7));
    ctx.lineTo(250 - 6, 250 - (outsideRadius - 7));
    ctx.lineTo(250 - 6, 250 - (outsideRadius + 7));
    ctx.fill();
  }
}

function spin() {
  spinAngleStart = Math.random() * 10 + 10;
  spinTime = 0;
  spinTimeTotal = Math.random() * 3 + 4 * 1000;
  rotateWheel();
}

function rotateWheel() {
  spinTime += 30;
  if(spinTime >= spinTimeTotal) {
    stopRotateWheel();
    return;
  }
  var spinAngle = spinAngleStart - easeOut(spinTime, 0, spinAngleStart, spinTimeTotal);
  startAngle += (spinAngle * Math.PI / 180);
  drawRouletteWheel();
  spinTimeout = setTimeout('rotateWheel()', 30);
}

function stopRotateWheel() {
  clearTimeout(spinTimeout);
  var degrees = startAngle * 180 / Math.PI + 90;
  var arcd = arc * 180 / Math.PI;
  var index = Math.floor((360 - degrees % 360) / arcd);
  ctx.save();
  ctx.font = 'bold 30px Roboto';
  var text = options[index]
  //ctx.fillText(text, 250 - ctx.measureText(text).width / 2, 250 + 10);
//premio(text);
alert(text);
  ctx.restore();
}

function easeOut(t, b, c, d) {
  var ts = (t/=d)*t;
  var tc = ts*t;
  return b+c*(tc + -3*ts + 3*t);
}

drawRouletteWheel();

function premio(text){
    console.log(text);
    $('#modal-show').modal('show');
    var premio = '<h1 style="font-weight: bolder;">'+text+'</h1>';
    $('#premio').html(premio);
}



</script>
