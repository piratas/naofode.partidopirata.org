<?php
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$parts = explode('/', $uri);
$code = array_pop($parts);
$title = "<strong>naofo.de</strong> | encurtador higiênico de chorume";

if ($code) {
    include_once("Thrash.class.php");
    if ($thrash = Thrash::get_by_code($code)) {
        include_once("Mobile_Detect.php");
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) { //the big <img> isn't working on iOS, don't know why.
            header("Location: ".$thrash->get_image_path());
            die();
        }
        $title = "<strong>naofo.de</strong> | {$thrash->title}";
    } else {
        die('codigo invalido');
    }
} else {
	if (isset($_POST['url'])) {
        include_once("Thrash.class.php");
		$thrash = Thrash::create($_POST['url'], $_POST['title']);
        header("Location: {$thrash->code}");
        die();
	}
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo strip_tags($title); ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">

        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>
        $(function() {

            $("form input[type=submit]").click(function() {
                var url = $("input[name=url]").val();
                if (!url) return;
                $('div#mask').fadeIn(333);
                if ($(this).val() == 'gerar') return true;
                if (url.indexOf('http') != 0) {
                    url = 'http://'+url;
                    $("input[name=url]").val(url);
                }
                $.get('proxy.php?url='+encodeURIComponent(url), function(response) {
                    var regex = (/<title>(.*?)<\/title>/m).exec(response);
                    if (regex != null) {
                        title = regex[1];
                        $("textarea").val(title);
                    }
                    $("fieldset.title").show();
                    $("form input[type=submit]").val('gerar');
                    $("form").addClass('ready');
                    $('div#mask').fadeOut(333);
                });
                return false;
            });
            $("form").submit(function() {
                ga('send', 'event', 'action', 'generate', $("input[name=url]").val());
            });
        });
        </script>

    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">
            <header class="wrapper clearfix">
                <h1 class="title"><?php echo $title; ?></h1>
                <nav>
                    <ul>
                        <li><a href="./">encurtar url</a></li>
                        <li><a href="#reclameaqui" onclick="alert('¯\\_(ツ)_/¯');">reclamações</a></li>
                    </ul>
                </nav>
            </header>
        </div>

        <?php
        if (isset($thrash) && $thrash) {
            ?>
            <p>URL original: <?php echo htmlentities($thrash->original_url); ?></p>
            <meta property="og:image" content="<?php echo $thrash->get_image_path(); ?>"/>
            <meta property="og:title" content="<?php echo $title; ?>"/>
            <div id="fb-root"></div>
            <script>(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1397618427132106";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
            <div class="share">
                <div>Compartilhe este naofo.de:</div>
                <div class="fb-share-button" data-href="<?php echo $_SERVER['REQUEST_URI']; ?>" data-type="button_count"></div>
                <a href="https://twitter.com/share" class="twitter-share-button" data-lang="pt">Tweetar</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
            </div>
            <img src="<?php echo $thrash->get_image_path(); ?>" />
            <?php
        } else {
            ?>
            <div id="mask"></div>
            <div class="main-container">
                <div class="main wrapper clearfix">
                    <article>
                        <!--div>* ESTAMOS EM FASE DE TESTES * Pode ser que o servidor não aguente o tranco, cruze os dedos daí que eu postergo uma solução daqui</div-->
                    	<h1>Encurtar url</h1>
                    	<p>
                    		<form method="post">
                    			<input type="submit" value="prosseguir" />
                    			<fieldset>Endereço: <input type="text" name="url" /></fieldset>
                    			<!--fieldset><input type="checkbox" name="display_url" value="1" /> <small>exibir endereço original</small></fieldset-->
                                <fieldset class="title"><label>Título:</label><textarea name="title"></textarea></fieldset>
                    		</form>
                    	</p>
                    </article>

                    <aside>
                        <h3>O que é?</h3>
                        <p>O naofo.de é um serviço de compartilhamento/encurtamento de URLs com propósito de denúncia/comentário crítico. Em vez da página original, a URL encurtada direciona para uma cópia (em imagem) do conteúdo, de modo que não se aumentará o tráfego ou o <em>pagerank</em> da página em questão. Além disso, a cópia ficará disponível mesmo que a página original seja tirada do ar.</p>
                        <p><strong>IMPORTANTE</strong><br/>
                        	NÃO compartilhe conteúdo ilegal e criminoso, tais como pedofilia ou exposição vexatória de pessoas, por este serviço. O conteúdo será retirado do ar e seu acesso ao serviço barrado. Eventualmente forneceremos seu IP a autoridades competentes.
                        	Há outras formas de denunciar e é criminosa (além de contraproducente) a reprodução desses conteúdos. Use, por exemplo: <a href="http://www.dpf.gov.br/servicos/fale-conosco/denuncias">http://www.dpf.gov.br/servicos/fale-conosco/denuncias</a>
                            <p>
                            Tampouco use o naofo.de como um encurtador comum: fazer cópia do conteúdo implica em custos de servidor, e um crescimento da demanda tornaria inviável o serviço, que jamais terá fins lucrativos. Por favor, use apenas para denúncia e compartilhamento de conteúdo desprezível.
                            </p>
                        </p>
                    </aside>
                </div> <!-- #main -->
            </div> <!-- #main-container -->
            <?php
        }
        ?> 

        <div class="footer-container">
            <footer class="wrapper">
                <a href="https://github.com/pedromoraes/naofo.de">código fonte</a>
            </footer>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create', 'UA-50387468-1', 'naofo.de');ga('send','pageview');
        </script>
    </body>
</html>
