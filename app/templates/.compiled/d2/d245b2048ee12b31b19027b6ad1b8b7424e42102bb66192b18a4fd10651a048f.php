<?php

/* home/home.twig */
class __TwigTemplate_3a1b739c6fa6b9a9053fd9857944258d195315d310a8aa535195169d74e8e3bb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("overall/layout", "home/home.twig", 1);
        $this->blocks = array(
            'appBody' => array($this, 'block_appBody'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "overall/layout";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_appBody($context, array $blocks = array())
    {
        // line 3
        echo "    <div class=\"box-ocrend\">
        <h1>OCREND FRAMEWORK 3</h1>
        <p>Bienvenido ¡empecemos a programar!</p>
        ";
        // line 6
        if (($context["is_logged"] ?? null)) {
            // line 7
            echo "            <p>Sesión iniciada como <b>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["owner_user"] ?? null), "name", array()), "html", null, true);
            echo "::";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["owner_user"] ?? null), "id_user", array()), "html", null, true);
            echo "</b></p>
        ";
        }
        // line 9
        echo "        <div>
            <a href=\"https://framework.ocrend.com\" target=\"_blank\">Documentación</a>
            <span>-</span>
            <a href=\"https://framework.ocrend.com/donaciones\" target=\"_blank\">Donaciones</a>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "home/home.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 9,  38 => 7,  36 => 6,  31 => 3,  28 => 2,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "home/home.twig", "/home/admin/web/hospitalmetropolitano.org/public_html/e-hm/app/templates/home/home.twig");
    }
}
