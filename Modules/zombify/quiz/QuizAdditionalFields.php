<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( ! class_exists("ZF_QuizAdditionalFields") ) {

    /**
     * Class ZF_QuizAdditionalFields For adding additional fields to Zombify Quiz
     */
    class ZF_QuizAdditionalFields
    {
        const NAME_PREFIX = 'zf_ad_f';

        /**
         * Fields for storing html elements
         */
        private $ad_f_label,
            $ad_f_type,
            $ad_f_name,
            $ad_f_required,
            $ad_f_value,
            $ad_f_choices,
            $ad_f_placeholder,
            $ad_f_id,
            $ad_f_class,
            $ad_f_extra_attr;

        /**
         * Handles the situation when called method does not exist
         *
         * @param array $name
         * @param string $arguments
         *
         * @return void
         *
         * @throws Exception in case method does not exist
         */
        public function __call($name, $arguments)
        {
            throw new Exception("Method {$name} is not supported.");
        }

        /**
         * Call specific method for the field type
         *
         * @param array $data
         *
         * @return void
         */
        public function drawFields( $data ) {
            foreach( $data as $ad_f ) {
                if( ! empty( $ad_f['field_type'] ) && ! empty( $ad_f['field_name'] ) ) {
                    $this->initializeParameters( $ad_f );

                    $methodName = 'draw' . ucfirst( $ad_f['field_type'] );
                    $this->{$methodName}();
                }
            }
        }
        /**
         * Draw inputs of additional fields of Zombify post
         *
         * @param $data                 array   Array of fields with following items
         *      'field_type'            string  Field type, supported: text, hidden, checkbox, radio, also textarea and select
         *      'field_name'            string  Field name
         *      'field_label'           string  Field label
         *      'is_required'           bool    Whether required the field or not
         *      'field_value'           string  Field value
         *      'field_default_value'   string  Field default value
         *      'field_choices'         array   select->option/radio->input name and label as key=>value of assoc array
         *      'field_placeholder'     string  Field placeholder
         *      'field_class'           array   Field classes given as an numeric array
         *      'field_id'              string  Field id
         *      'extra_attr'            array   Extra attributes given as an associative array where
         *                                    key is the attribute name, value is the value
         *
         * @return void
         */
        private function initializeParameters( $data ) {
            $this->ad_f_label         = empty( $data['field_label'] ) ? '' : $this->getLabel( $data['field_label'] );
            $this->ad_f_type          = $this->getInputType( $data['field_type'] );
            $this->ad_f_name          = $this->getName( $data['field_name'] );
            $this->ad_f_required      = ( ! empty( $data['is_required'] ) && true === $data['is_required'] )
                ? $this->getRequired() : '';

            if( isset( $data['field_value'] ) ) {
                $this->ad_f_value = $data['field_value'];
            } else {
                $this->ad_f_value = isset( $data['field_default_value'] ) ? $data['field_default_value'] : '';
            }

            $this->ad_f_choices = empty( $data['field_choices'] ) ? false : (array) $data['field_choices'];

            $this->ad_f_placeholder   = empty( $data['field_placeholder'] ) ? '' : $this->getPlaceholder( $data['field_placeholder'] );
            $this->ad_f_id            = empty( $data['field_id'] ) ? '' : $this->getID( $data['field_id'] );

            $this->ad_f_class = ( empty( $data['field_class'] ) ) ? '' : $this->getClasses( (array) $data['field_class'] );

            $this->ad_f_extra_attr = empty( $data['extra_attr'] ) ? '' : $this->getExtraAttributes( (array) $data['extra_attr'] );

        }

        /**
         * Print out input typed text
         *
         * @return void
         */
        private function drawText() {
            ?>
            <div class="zf-option-item">
                <?php
                echo $this->ad_f_label . $this->getLabelCloseTag();
                echo "<input{$this->ad_f_type}{$this->ad_f_name}{$this->ad_f_required}{$this->getValue( $this->ad_f_value )}{$this->ad_f_placeholder}{$this->ad_f_id}{$this->ad_f_class}{$this->ad_f_extra_attr}>";
                ?>
            </div>
            <?php
        }

        /**
         * Print out input typed hidden
         *
         * @return void
         */
        private function drawHidden() {
            ?>
            <div class="zf-option-item">
                <?php
                echo "<input{$this->ad_f_type}{$this->ad_f_name}{$this->getValue( $this->ad_f_value )}{$this->ad_f_extra_attr}>";
                ?>
            </div>
            <?php
        }

        /**
         * Print out input typed radio
         *
         * @return void
         */
        private function drawRadio() {
            ?>
            <div class="zf-option-item">
                <?php
                foreach( $this->ad_f_choices as $value => $label ) {
                    $checked = ( $value == $this->ad_f_value ) ? ' checked="checked"' : '';
                    echo $this->getLabel();
                    echo "<input{$this->ad_f_type}{$this->ad_f_name}{$this->ad_f_required}{$this->getValue( $value )}{$checked}{$this->ad_f_class}{$this->ad_f_extra_attr}>{$label}";
                    echo $this->getLabelCloseTag();
                }
                ?>
            </div>
            <?php
        }

        /**
         * Print out input typed checkbox
         *
         * @return void
         */
        private function drawCheckbox() {
            ?>
            <div class="zf-option-item">
                <?php
                $checked = checked( $this->ad_f_value, true, false );
                echo $this->ad_f_label;
                echo '<input type="hidden"' . $this->ad_f_name . 'value="0"' . '>';
                echo "<input{$this->ad_f_type}{$this->ad_f_name}{$this->ad_f_required}{$this->getValue( 1 )}{$this->ad_f_id}{$this->ad_f_class}{$this->ad_f_extra_attr}{$checked}>";
                echo $this->getLabelCloseTag();
                ?>
            </div>
            <?php
        }

        /**
         * Print out select
         *
         * @return void
         */
        private function drawSelect() {
            ?>
            <div class="zf-option-item">
                <?php
                echo "<select{$this->ad_f_required}{$this->ad_f_id}{$this->ad_f_class}{$this->ad_f_extra_attr}>";
                foreach( $this->ad_f_choices as $value => $label ) {
                    $selected = ( $value == $this->ad_f_value ) ? ' selected="selected"' : '';
                    echo "<option{$this->ad_f_name}{$this->getValue( $value )}{$selected}>{$label}</option>";
                }
                ?>
                </select>
            </div>
            <?php
        }

        /**
         * Print out textarea
         */
        private function drawTextarea() {
            ?>
            <div class="zf-option-item">
                <?php
                echo $this->ad_f_label . $this->getLabelCloseTag();
                echo "<textarea{$this->ad_f_name}{$this->ad_f_placeholder}{$this->ad_f_required}{$this->ad_f_id}{$this->ad_f_class}{$this->ad_f_extra_attr}>{$this->ad_f_value}</textarea>";
                ?>
            </div>
            <?php
        }

        /**
         * Get field label
         *
         * @param string $label
         *
         * @return string
         */
        private function getLabel( $label = '' ) {
            return '<label class="zf-checkbox-inline">' . $label;
        }

        /**
         * Get `label` closing tag
         *
         * @return string
         */
        private function getLabelCloseTag() {
            return '</label>';
        }

        /**
         * Get input `type` attribute html
         *
         * @param string $type
         *
         * @return string
         */
        private function getInputType( $type ) {
            return ' type="' . $type . '"';
        }

        /**
         * Get `name` attribute html
         *
         * @param string $name
         *
         * @return string
         */
        private function getName( $name ) {
            return ' name="' . self::NAME_PREFIX . '[' . $name . ']"';
        }

        /**
         * Get `required` attribute html
         *
         * @return string
         */
        private function getRequired() {
            return ' required="required"';
        }

        /**
         * Get `placeholder` attribute html
         *
         * @param string $placeholder
         *
         * @return string
         */
        private function getPlaceholder( $placeholder ) {
            return ' placeholder="' . $placeholder . '"';
        }

        /**
         * Get `id` attribute html
         *
         * @param string $id
         *
         * @return string
         */
        private function getID( $id ) {
            return ' id="' . $id . '"';
        }

        /**
         * Get `class` attribute html
         *
         * @param array $classes
         *
         * @return string
         */
        private function getClasses( $classes ) {
            $ad_f_class = ' class="';
            foreach( $classes as $ad_f_c ) {
                $ad_f_class .= $ad_f_c . ' ';
            }
            $ad_f_class = trim( $ad_f_class );
            $ad_f_class .= '"';

            return $ad_f_class;
        }

        /**
         * Get extra attributes html
         *
         * @param array $attr
         *
         * @return string
         */
        private function getExtraAttributes( $attr ){
            $ad_f_extra_attr = '';
            foreach( $attr as $ad_f_attr_name => $ad_f_attr_value ) {
                $ad_f_extra_attr .= ' ' . $ad_f_attr_name . '="' . $ad_f_attr_value . '"';
            }

            return $ad_f_extra_attr;
        }

        /**
         * Get `value`
         *
         * @param string $value
         *
         * @return string
         */
        private function getValue( $value ) {
            return ( ! empty( $value ) ) ? ' value="' . $value . '"' : '';
        }
    }
}