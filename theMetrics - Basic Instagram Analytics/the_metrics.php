<?php
    session_start();

    define('MINIMUM_FOLLOWERS', '1');

    $followers = 0;
    $following = 0;
    $medianEngagement = 0;
    $averageEngagement = 0;
    $totalLikes = 0;
    $totalComments = 0;
    $engagement = 0;
    $likesPerPic = 0;
    $commentsPerPic = 0;

    function scrape_insta($username) 
    {
        if(isset($username))
        {
            $insta_source = file_get_contents('http://instagram.com/'.$username);
            $shards = explode('window._sharedData = ', $insta_source);
            $insta_json = explode(';</script>', $shards[1]); 
            $insta_array = json_decode($insta_json[0], TRUE);
            return $insta_array;
        }
    }

    if(isset($_POST['username']))
    {
        $my_account = $_POST['username'];

        //Do the deed
        $results_array = scrape_insta($my_account);

        //An example of where to go from there
        $latest_array = $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][0];

        //echo entire json object neatly formatted (Really Useful)
        //echo '<pre>'; print_r($results_array); echo '</pre>';

        //check if account is private
        $isPrivate = $results_array['entry_data']['ProfilePage'][0]['user']['is_private'];

        //get user info
        $username = $results_array['entry_data']['ProfilePage'][0]['user']['username'];
        $followers = $results_array['entry_data']['ProfilePage'][0]['user']['followed_by']['count'];
        $following = $results_array['entry_data']['ProfilePage'][0]['user']['follows']['count'];
        $fullName = $results_array['entry_data']['ProfilePage'][0]['user']['full_name'];
        $biography = $results_array['entry_data']['ProfilePage'][0]['user']['biography'];
        $profilePic = $results_array['entry_data']['ProfilePage'][0]['user']['profile_pic_url'];
        
        //get media and media info
        $numImages = $results_array['entry_data']['ProfilePage'][0]['user']['media']['count'];


        //collect images
        $numRequested = 20;
        $mediaNodes = array();
        for ($i=0; $i < $numRequested; $i++)
        { 
            array_push($mediaNodes, $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][$i]);
        }

        /////////////////////////////////////////////////////////////////////////////////////////
        function printImages($mediaNodes)
        {
            //loop through returned data
            foreach($mediaNodes as $post)
            {
                $imageURL = $post['thumbnail_src'];
                echo '<div  class="col-lg-2 col-md-4 col-sm-5 text-center" id = "recentImg">';
                echo '<div class="service-box">';
                echo '<img style = "margin: 0 auto 0" class = "img-responsive img-circle" src=" '.$post['thumbnail_src'].' "/>';
                echo "</div>";
                echo "</div>";
            }
        }

        echo "likes:";
        foreach($mediaNodes as $post)
        {
            $totalLikes += $post['likes']['count'] ;
            echo $post['likes']['count']. '</br>';
        }
        echo $totalLikes . '</br>';

        //get total comments for num requested
        echo "Comments:";
        foreach($mediaNodes as $post)
        {
            $totalComments += $post['comments']['count'] ;
            echo $post['comments']['count']. '</br>';
        }
        echo $totalComments . '</br>';

        //get Average Engagement for num requested
        //likes are the closest thing to Instagram telling you that someone stopped to view your pic
        echo 'Average Engagement: </br>';
        $averageEngagement = $totalLikes/$followers;
        echo $averageEngagement;
        
        $likesPerPic = $totalLikes/$numRequested;        
        echo "likes per pic: </br>";
        echo $likesPerPic .'</br>';

        $commentsPerPic = $totalComments/$numRequested;
        echo "comments per pic: </br>";
        echo $commentsPerPic .'</br>';

        function createLikesArray()
        {
            $likesArray = array();
            foreach($mediaNodes as $post)
            { 
                array_push($likesArray, $post['likes']['count']);
            }
        }

        function createCommentsArray()
        {
            $commentsArray = array();
            foreach($mediaNodes as $post)
            { 
                array_push($commentsArray, $post['comments']['count']);
            }
        }
    }
?>

<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title> The Metrics - Basic Instagram Analytics</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/testApp.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Poiret+One" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../font-awesome-4.6.3/css/font-awesome.min.css" type="text/css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="../css/animate.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/creative.css" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway" rel="stylesheet">

    <!-- jQuery -->
    <script src="../js/jquery.js"></script>
    <script src="../js/bootbox.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top">
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="../index.html">The Metrics</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <script type="text/javascript">
            var about = "";
            about += "The Metrics, a little side project I made for Instagram analytics without the API. Enjoy!"

        $(document).ready(function(){
                $("#requirements").on("click", function(){
                    bootbox.alert(about);
                });
            });
    </script>

    <header style="background-image: url(../img/ivCamHeader.jpg)">
        <div class="header-content">
            <div class="header-content-inner">
                <?php 
                //display username
                if (isset($username))
                {
                    echo "<h1 style='font-weight: 100; color:#222'>".$username."</h1>";
                }
                else
                {
                    echo "<h1 style='font-weight: 100; color:#222'>Welcome to The Metrics</h1>";
                }
                ?>

                <hr>
                </br>
                </br>
                 <p style="color:#222">
                    <?php
                        //bio display attempt
                        if (isset($biography))
                        {
                            echo $biography;
                        }
                        else
                        {
                        echo "Basic Instagram analytics on any public Instagram account. Just enter the Instagram handle.";
                        }
                    ?>
                </p>

                <?php
                    //attempt to display profile pic
                    if (isset($profilePic))
                    {
                        echo '<div id = "profilePic">';
                        echo '<img id = "profilePic" class="img-rounded img-responsive" src=" '.$profilePic.' "/>';
                        echo "</div>";
                    }
                    else 
                    {
                        echo "<div class = 'col-lg-6 col-lg-offset-3'><form action ='the_metrics.php' method ='post'><div class ='input-group'><input name = 'username' class='form-control input-md' placeholder='Enter Instagram Handle' type='text'></input><span class = 'input-group-btn'><input id = 'handle' type = 'submit' class='btn btn-primary btn-md page-scroll' value = 'Submit'></input></span></div></form></div>";
                    }
                ?>
            </div>
                    </br>
                    </br>
                    </br>
                    <p style="color:#222"><a style="color:#286090" id="requirements" href="#">About The Metrics</a></p>
        </div>
    </header>
    
    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-lg-offset-2 text-center">
                    <h1> Followers </h1>
                    <p><h3><?php echo $followers; ?></h3></p>
                </div>
                <div class="col-lg-4 text-center">
                    <h1> Following </h1>
                    <p><h3><?php echo $following; ?></h3></p>
                </div>
            </div>
        </div>
    </section>
    
    

    <div id = "instafeed" class="container">
        <?php 
        if(isset($_POST['username']))
        { 
            printImages($mediaNodes);
        }
     ?>
    </div>

        </br>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">These are the metrics</h2>
                    <hr class="primary">
                </div>
            </div>
        </div>

    <div class="container" id = "HowItWorks">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2 text-center">
                    <div class="service-box">
                        <i class="fa fa-4x fa-thumbs-o-up wow bounceIn text-primary"></i>
                        <h3>Total Likes</h3>
                        <p class="text-muted"> <?php echo $totalLikes; ?> </p>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2 text-center">
                    <div class="service-box">
                        <i class="fa fa-4x fa-comments wow bounceIn text-primary" data-wow-delay=".1s"></i>
                        <h3>Total Comments</h3>
                        <p class="text-muted"><?php echo $totalComments; ?></p>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2 text-center">
                    <div class="service-box">
                        <i class="fa fa-4x fa-thumbs-up wow bounceIn text-primary" data-wow-delay=".2s"></i>
                        <h3>Likes Per Post</h3>
                        <p class="text-muted"><?php echo $likesPerPic; ?></p>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2 text-center">
                    <div class="service-box">
                        <i class="fa fa-4x fa-comment wow bounceIn text-primary" data-wow-delay=".3s"></i>
                        <h3>Comments Per Post</h3>
                        <p class="text-muted"><?php echo $commentsPerPic; ?></p>
                    </div>
                </div>
            </div>
        </div>

    <section id="contact" class = "articles">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">Get your numbers up!</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-primary" id="about">
        <div class="container">
            <div class="container" id = "HowItWorks">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-md-offset-4 text-center">
                    <div class="service-box">
                        <i style="color : #222" class="fa fa-4x fa-map-signs wow bounceIn text-primary" data-wow-delay=".1s"></i>
                        <h3 style="color:222">Average Engagement</h3>
                        <p class="text-muted"><?php echo $averageEngagement ."%"; ?><p/>
                    </div>
                </div>
        </div>


        </div>
    </section>

    <?php
    /*Logout button*/
    if (isset($userID))
    {
    echo'<aside style="background-color:#fff" class="bg-dark">';
        echo'<div class="container text-center">';
            echo'<div class="call-to-action">';
                echo'<h2 style="color:#222"></h2>';
                echo'<a href="index.php" style="background-color:#f5f5f5" class="btn btn-default btn-xl wow tada">Logout</a>';
            echo'</div>';
        echo'</div>';
    echo'</aside>';
    }
    ?>

    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">Thanks For Using The Metrics</h2>
                    <hr class="primary">
                    <p>More projects coming soon.</p>
                </div>
                <div class="col-lg-12 text-center">
                    <i class="fa fa-envelope-o fa-3x wow bounceIn" data-wow-delay=".1s"></i>
                    <p><a href="mailto:bkulani@gmail.com">bkulani@gmail.com</a></p>
                </div>
            </div>
        </div>
    </section>


    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="../js/jquery.easing.min.js"></script>
    <script src="../js/jquery.fittext.js"></script>
    <script src="../js/wow.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../js/creative.js"></script>



</body>

</html>