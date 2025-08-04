<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Configuration block for the color picker in the admin panel.
 * This block renders a color input field with synchronization between
 * the visual picker and the text field.
 */
class ColorPicker extends Field
{
    private const DEFAULT_COLOR = '#000000';

    /**
     * Renders the HTML of the configuration element.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $colorValue = $this->getSanitizedColor($element->getData('value'));
        $htmlId = $element->getHtmlId();
        $inputName = $element->getName();

        return $this->getColorPickerHtml($htmlId, $inputName, $colorValue)
            . $this->getSyncScript($htmlId);
    }

    /**
     * Generates the HTML for the color picker and the text field.
     *
     * @param string $htmlId
     * @param string $inputName
     * @param string $colorValue
     * @return string
     */
    private function getColorPickerHtml(string $htmlId, string $inputName, string $colorValue): string
    {
        return sprintf(
            '<input type="color" id="%1$s_picker" value="%2$s" style="width:40px;height:34px;vertical-align:middle;margin-right:10px;" />' .
            '<input type="text" id="%1$s" name="%3$s" value="%2$s" style="width:100px;vertical-align:middle;" maxlength="7" pattern="^#([A-Fa-f0-9]{6})$" />',
            htmlspecialchars($htmlId, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($colorValue, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($inputName, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Generates the JS script to synchronize the color picker and text inputs.
     *
     * @param string $htmlId
     * @return string
     */
    private function getSyncScript(string $htmlId): string
    {
        $escapedId = addslashes($htmlId);
        return <<<HTML
            <script>
            require(['jquery'], function($) {
                var \$picker = $("#{$escapedId}_picker");
                var \$input = $("#{$escapedId}");
                \$picker.on("input", function() {
                    \$input.val(\$picker.val());
                });
                \$input.on("input", function() {
                    var val = \$input.val();
                    if (/^#([A-Fa-f0-9]{6})$/.test(val)) {
                        \$picker.val(val);
                    }
                });
            });
            </script>
            HTML;
    }

    /**
     * Sanitizes the color value to ensure it is a valid hex color.
     * If the value is not valid, it returns the default color.
     *
     * @param string|null $value
     * @return string
     */
    private function getSanitizedColor(?string $value): string
    {
        if (is_string($value) && preg_match('/^#([A-Fa-f0-9]{6})$/', $value)) {
            return $value;
        }
        return self::DEFAULT_COLOR;
    }
}
