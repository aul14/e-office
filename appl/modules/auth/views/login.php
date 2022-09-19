<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>e-Office | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo assets_url(); ?>/media/icon_eoffice.png">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?php echo assets_url(); ?>/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo assets_url(); ?>/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo assets_url(); ?>/plugins/iCheck/square/blue.css">
    <style type="text/css">
    	.form-login {
    		background:url(/lx_media/login_BG.jpg); 
    		background-repeat:no-repeat;
   			background-size:100% 100%;
    	}
    	.color-logo {
   			color: #660000;
  		}
		.color-logo:hover, .color-logo:active {
			color: #333 !important;
		}
  		.color-button {
     		background-color: #660000;
			border-color:#E9967A;
  		}
		.color-button:hover, .color-button:active, .color-button.hover {
			background-color: #444 !important;
			border-color:#ccc !important;
		}
		.form-control {
	    	background-color: #f8f3ed;
	    }
		.form-control:focus {
			border-color: #E9967A;
		}
	</style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="form-login hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <!-- b>e-Office</b-->
        <img src="/lx_media/Logo_text_baru.png" alt="e-Office" style="width: 230px;"/>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"><i class="glyphicon glyphicon-lock color-logo" style="font-size: 34pt;"></i></p>
        <form class="form" action="<?php echo site_url('auth/login/authenticate'); ?>" method="post">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" name="email" placeholder="eMail...">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" name="password" placeholder="Password...">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-4 offset-xs-8">
              <button type="submit" class="btn btn-primary btn-block btn-flat color-button">Sign In</button>
            </div><!-- /.col -->
          </div>
        </form>

        <div class="pull-right">
        <a href="<?php echo site_url('auth/login/forgot_password'); ?>" class= "color-logo">I forgot my password</a></div>
        <div></div>
        <br>
        
        <?php echo validation_errors(); ?>
        <?php echo $msg; ?> 
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="<?php echo assets_url(); ?>/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?php echo assets_url(); ?>/bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="<?php echo assets_url(); ?>/plugins/iCheck/icheck.min.js"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>
