<?php

/* overall/layout.twig */
class __TwigTemplate_78aa5ea624013391b20ef940d0a0cfc14ec3d4feed96ef3777b7240e11fb69d2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'appHeader' => array($this, 'block_appHeader'),
            'appBody' => array($this, 'block_appBody'),
            'appFooter' => array($this, 'block_appFooter'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"es\">

<head>
    ";
        // line 5
        echo " ";
        echo $this->env->getExtension('Ocrend\Kernel\Helpers\Functions')->base_assets();
        echo "
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\"> ";
        // line 8
        echo " ";
        // line 9
        echo "    <link rel=\"stylesheet\" href=\"assets/framework/main.min.css\" /> ";
        $this->displayBlock('appHeader', $context, $blocks);
        // line 10
        echo "    <title>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), twig_get_attribute($this->env, $this->getSourceContext(), ($context["config"] ?? null), "build", array()), "name", array()), "html", null, true);
        echo "</title>
</head>

<body>
    ";
        // line 14
        $this->displayBlock('appBody', $context, $blocks);
        echo " ";
        // line 15
        echo "    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
    ";
        // line 16
        $this->displayBlock('appFooter', $context, $blocks);
        // line 17
        echo "    <script type=\"text/javascript\">
    function f() {

        \$.ajax({
            type: 'GET', // POST o GET
            url: 'api/pacientes',
            data: {
                'user': 'sdsd'
            },
            success: function(json) {
                console.log(json);
            },
            error: function(xhr, status) {
                console.log('Ha ocurrido un problema interno');
            }
        });

    }

    f()
    </script>
</body>

</html>";
    }

    // line 9
    public function block_appHeader($context, array $blocks = array())
    {
        echo " ";
    }

    // line 14
    public function block_appBody($context, array $blocks = array())
    {
        echo " ";
    }

    // line 16
    public function block_appFooter($context, array $blocks = array())
    {
        echo " ";
    }

    public function getTemplateName()
    {
        return "overall/layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 16,  89 => 14,  83 => 9,  56 => 17,  54 => 16,  51 => 15,  48 => 14,  40 => 10,  37 => 9,  35 => 8,  28 => 5,  22 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html lang=\"es\">

<head>
    {# Formato #} {{ base_assets()|raw }}
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\"> {# Estilo definido por defecto para presentar el framework #} {# No es necesaria la existencia de este archivo, puede ser borrado #}
    <link rel=\"stylesheet\" href=\"assets/framework/main.min.css\" /> {% block appHeader %} {% endblock %}
    <title>{{ config.build.name }}</title>
</head>

<body>
    {% block appBody %} {% endblock %} {# Sólamente necesario para facilitar las peticiones AJAX del generador, puede eliminarse #}
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
    {% block appFooter %} {% endblock %}
    <script type=\"text/javascript\">
    function f() {

        \$.ajax({
            type: 'GET', // POST o GET
            url: 'api/pacientes',
            data: {
                'user': 'sdsd'
            },
            success: function(json) {
                console.log(json);
            },
            error: function(xhr, status) {
                console.log('Ha ocurrido un problema interno');
            }
        });

    }

    f()
    </script>
</body>

</html>", "overall/layout.twig", "/home/admin/web/api.hospitalmetropolitano.org/public_html/app/templates/overall/layout.twig");
    }
}
