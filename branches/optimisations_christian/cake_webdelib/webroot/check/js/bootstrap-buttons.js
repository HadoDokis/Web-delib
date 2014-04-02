<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Franckyz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Francky359">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/bootstrap-franckyz.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body>
    <!-- NAVBAR
    ================================================== -->
    <!-- Wrap the .navbar in .container to center it on the page and provide easy way to target it with .navbar-wrapper. -->
    <div class="container navbar-wrapper">

      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <!-- Responsive Navbar Part 1: Button for triggering responsive navbar (not covered in tutorial). Include responsive CSS to utilize. -->
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Franckyz.com</a>
          <!-- Responsive Navbar Part 2: Place all navbar contents you want collapsed withing .navbar-collapse.collapse. -->
          <div class="nav-collapse collapse">
            <ul class="nav">
              <!--<li class="active"><a href="#">Accueil</a></li>-->
              <li><a href="#contact">Contact</a></li>
              <!--<li><a href="#about">A propos</a></li>-->
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!-- /.navbar-inner -->
      </div><!-- /.navbar -->

    </div><!-- /.container -->
    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide">
      <div class="carousel-inner">
        <div class="item active">
          <img src="img/dc01.jpg" alt="slide-01">
          <div class="container">
            <div class="carousel-caption">
              <h1>IT System</h1>
              <p class="lead">Infrastructure, Hebergement, Cloud.</p>
              <a class="btn btn-large btn-primary" href="#">Se connecter</a>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="img/dc02.jpg" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>Réseaux</h1>
              <p class="lead">Fibre.</p>
            <!--  <a class="btn btn-large btn-primary" href="#">Learn more</a>-->
            </div>
          </div>
        </div>
        <div class="item">
          <img src="img/dc03.jpg" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>Stockage.</h1>
              <p class="lead">San, Fcoe, Iscsi, Dedup.</p>
              <!--<a class="btn btn-large btn-primary" href="#">Browse gallery</a>-->
            </div>
          </div>
        </div>
      </div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
    </div><!-- /.carousel -->    
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
    <script>
      !function ($) {
        $(function(){
          // carousel demo
          $('#myCarousel').carousel()
        })
      }(window.jQuery)
    </script>
  </body>
</html>
    <div class="container marketing">
      <!-- Three columns of text below the carousel -->
      <div class="row">
        <div class="span2 offset 1">
	    <a class="btn btn-large btn-danger"href="http://munin.franckyz.com" ><i class="icon-time icon-white"></i><br>Monitoring</a>
	 </div><!-- /.span2 -->
        <div class="span2 offset1">
        <a class="btn btn-large btn-success" href="http://stats.franckyz.com"><i class="icon-signal icon-white"></i><br>Statistiques</a>
	</div><!-- /.span2 -->
        <div class="span2 offset1">
        <a class="btn btn-large btn-warning" href="http://dl.franckyz.com"><i class="icon-time icon-white"></i><br>Download</a>
        </div><!-- /.span2 -->        
        <div class="span2 offset1">
        <a class="btn btn-large btn-primary" href="http://cloud.franckyz.com"><i class="icon-globe icon-white"></i><br>Cloud</a>
	</div><!-- /.span2--> 
     </div><!-- /.row -->
   </div><!-- /.container -->
<hr>
      <!-- FOOTER -->
	<div id="footer">
      <div class="container">
        <p class="muted credit" align=center>Copyright 2012<a href="http://www.franckyz.com"> FranckyZ</a></p>
      </div>
    </div>  
  </body>
</html>
