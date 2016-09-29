<?php
    session_start();

    ///////////////////////////////////////////////////////////////////////////////////
    //                                                                               //
    //    Simple instagram login using Instagram API, login,                         //
    //    display basic user info and a few images, also details such as             //
    //    follower count, etc cannot be displayed without review and approval from   //
    //    Instagram                                                                  //
    //                                                                               //
    //    NB  You'll need to supply your own client ID, client secret and            //
    //    redirect URI                                                               //
    //                                                                               //
    ///////////////////////////////////////////////////////////////////////////////////

    //define constants
    define('clientID', '');                      
    define('clientSecret', '');                  
    define('redirectURI', '');                         

    //funcion to connect to instagram to make requests
    function connectToInsta($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(

        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2,
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //display images
    function printImages($userID, $accessToken)
    {
        $url = "https://api.instagram.com/v1/users/".$userID."/media/recent/?access_token=".$accessToken."&count=18";
        $instagramInfo = connectToInsta($url);
        $results = json_decode($instagramInfo, true);

        //loop through returned data
        foreach ($results['data'] as $items)
        {
            $imageURL = $items['images']['low_resolution']['url'];
            echo '<div  class="col-lg-2 col-md-4 col-sm-5 text-center" id = "recentImg">';
            echo '<div class="service-box">';
            echo '<img style = "margin: 0 auto 0" class = "img-responsive img-circle" src=" '.$imageURL.' "/>';
            echo "</div>";
            echo "</div>";
        }
    }

    //GET the code and swap the code for an access token
    if(isset($_GET['code']))
    {

        $code = $_GET['code'];
        $url = "https://api.instagram.com/oauth/access_token";
        $access_token_settings = array('client_id' => clientID,
                                        'client_secret' => clientSecret,
                                        'grant_type' => 'authorization_code', 
                                        'redirect_uri' => redirectURI,
                                        'code' => $code
                                        );

        //make calls (cURL)
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($curl);
        curl_close($curl);

        $results = json_decode($result, true);

        if(isset($results['access_token']))
        {
            $accessToken = $results['access_token'];
        }
        
        /*Get user details*/

        //get username
        if(isset($results['user']['username']))
        {
            $username = $results['user']['username'];
        }

        //get user ID
        if(isset($results['user']['id']))
        {
            $userID = $results['user']['id'];
        }

        //get full name
        if(isset($results['user']['full_name']))
        {
            $fullName = $results['user']['full_name'];
        }

        //get bio
        if(isset($results['user']['bio']))
        {
            $bio = $results['user']['bio'];
        }

        //get profile picture url
        if(isset($results['user']['profile_picture']))
        {
            $profilePic = $results['user']['profile_picture'];
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

    <title>Instagram Sign In Page</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/testApp.css" type="text/css">

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../font-awesome-4.6.3/css/font-awesome.min.css" type="text/css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="../css/animate.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/creative.css" type="text/css">

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
                <a class="navbar-brand page-scroll" href="#page-top"><i style='color:#222' class='fa fa-key fa-1x wow bounceIn' data-wow-delay='.1s'></i></a>
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

    <header style="background-image: url(../img/ivCamHeader.jpg)">
        <div class="header-content">
            <div class="header-content-inner">
                <?php 
                //display username
                if (isset($username))
                {
                    echo "<h1 style='color:#222'>".$username."</h1>";
                }
                else
                {
                    echo "<i style='color:#222' class='fa fa-key fa-4x wow bounceIn' data-wow-delay='.1s'></i>";
                }
                ?>

                <hr>
                </br>
                </br>
                 <p style="color:#222">
                    <?php
                        //bio display attempt
                        if (isset($bio))
                        {
                            echo $bio;
                        }
                       else
                        {
                        echo "Simple sign in & posted images display using the Instagram API </br>
                                PS. This page has limited functionality as Instagram doesn't allow display of follwer/following count, amongst other things, without first reviewing and approval, and so, this is all I decided to create using the API. Anyway, enjoy!";
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
                        echo "<a href='https:api.instagram.com/oauth/authorize/?client_id=".clientID."&redirect_uri=".redirectURI."&response_type=code' class='btn btn-primary btn-xl page-scroll'><i class='fa fa-instagram'></i>Sign In With Instagram</a>";
                    }
                ?>
            </div>
        </div>
    </header>

    <div id = "instafeed" class="container">
        <?php 
        if (isset($userID) && isset($accessToken))
        { 
            echo printImages($userID, $accessToken);
        }
     ?>
    </div>

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
                    <h2 class="section-heading">Thanks for checking this page out</h2>
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

    <!-- jQuery -->
    <script src="../js/jquery.js"></script>

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
