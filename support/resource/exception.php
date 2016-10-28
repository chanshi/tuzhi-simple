<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <?php echo $exception->getMessage()?> -->
    <title>Exception - tuzhi (simple)</title>

    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Sticky footer styles
-------------------------------------------------- */
        html {
            position: relative;
            min-height: 100%;
        }
        body {
            /* Margin bottom by footer height */
            margin-bottom: 60px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            /* Set the fixed height of the footer here */
            height: 60px;
            background-color: #f5f5f5;
        }


        /* Custom page CSS
        -------------------------------------------------- */
        /* Not required for template or sticky footer method. */

        body > .container {
            padding: 60px 15px 0;
        }
        .container .text-muted {
            margin: 20px 0;
        }

        .footer > .container {
            padding-right: 15px;
            padding-left: 15px;
        }

        code {
            font-size: 80%;
        }


    </style>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Tuzhi (simple)</a>
        </div>
        <!--div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<!-- Begin page content -->
<div class="container">
    <div class="page-header">
        <h1>
            <?php if( method_exists($exception,'getName') ){?>
                <?= $exception->getName();?>
            <?php }else{?>
                Exception
            <?php }?>
        </h1>
    </div>
    <p class="lead"><code> <?php echo $exception->getMessage()?> </code> </p>
    <p> <kbd> <?= $exception->getLine() ?></kbd> <?php echo $exception->getFile()?> </p>
    <br>
    <br>
    <?php if($exception->getTrace()){?>
    <div>
        <h2>TraceMessage</h2>
        <table class="table table-striped table-hover">
            <?php $index = 1; foreach( $exception->getTrace() as $item){ ?>
                <?php if( in_array( $item['function'],
                    [
                        'autoload',
                        'spl_autoload_call',
                        'class_exists',
                        'call_user_func_array',
                        'call_user_func',
                        '__callStatic',
                        '__call'
                    ]
                ) ){ continue; }?>
                <tr>
                    <td><h4> #<?= $index++?></h4></td>
                    <td>
                        <p class="lead">
                            <?= isset($item['class']) ?$item['class']: null ?>
                            <?= isset($item['type']) ?$item['type']: null ?>
                            <?= isset($item['function']) ? $item['function'].'( )' : null ?>
                        </p>
                        
                        <?php if( isset($item['file']) ){?>
                        <p>
                            <kbd><?= isset($item['line']) ? $item['line'] : null ?></kbd>
                            <?= isset($item['file']) ? $item['file'] : null ?>
                        </p>
                        <?php }?>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
    <?php }?>
    <?php //print_r($exception)?>

</div>

<footer class="footer">
    <div class="container">
        <p class="text-muted">禅师</p>
    </div>
</footer>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>

