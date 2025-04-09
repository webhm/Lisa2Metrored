<?php

/* error/404.twig */
class __TwigTemplate_bd4d58f19a9e7999a96ab2d5a15aec1a1201c57a96e99a13b03cd7bde17254e7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("overall/layout", "error/404.twig", 1);
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
        <h1>ERROR 404</h1>
        <p>¡La página solicitada no ha sido encontrada!</p>
    </div>
";
    }

    public function getTemplateName()
    {
        return "error/404.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 3,  28 => 2,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "error/404.twig", "/home/admin/web/hospitalmetropolitano.org/public_html/e-hm/app/templates/error/404.twig");
    }
}
