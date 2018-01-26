<?php

namespace League\HTMLToMarkdown\Converter;

use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\ConfigurationAwareInterface;
use League\HTMLToMarkdown\ElementInterface;

class EmphasisConverter implements ConverterInterface, ConfigurationAwareInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @param Configuration $config
     */
    public function setConfig(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @param ElementInterface $element
     *
     * @return string
     */
    public function convert(ElementInterface $element)
    {
        $tag = $element->getTagName();
        $value = $element->getValue();

        if (!trim($value)) {
            return '';
        }

        $used_emphasis_option = array();
        $enforced_intraword_options = array('italic_style' => '*', 'bold_style' => '**');

        if ($tag === 'i' || $tag === 'em') {
            $used_emphasis_option['italic_style'] = $this->config->getOption('italic_style');
        } else {
            $used_emphasis_option['bold_style'] = $this->config->getOption('bold_style');
        }

        if (!array_intersect_assoc($used_emphasis_option, $enforced_intraword_options) && $element->isEmphasInsideWord()) {
            $used_emphasis_option = array_intersect_key($enforced_intraword_options, $used_emphasis_option);
        }

        $style = array_shift($used_emphasis_option);
        $prefix = ltrim($value) !== $value ? ' ' : '';
        $suffix = rtrim($value) !== $value ? ' ' : '';

        return $prefix . $style . trim($value) . $style . $suffix;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags()
    {
        return array('em', 'i', 'strong', 'b');
    }
}
