<?php
/**
 * Class Application_Form_Export
 */
class Application_Form_Export extends Siberian_Form_Abstract
{

    public function init()
    {
        parent::init();

        $this
            ->setAction(__path('/application/customization_features/export'))
            ->setAttrib('id', 'form-application-export');

        /** Bind as a onchange form */
        self::addClass('create', $this);
    }

    public function addOptions($application)
    {
        $opts = [];
        $elements = [];

        $allOptions = $application->getOptions();

        $globalMarkup = '
<style type="text/css">
    #form-application-export fieldset {
        padding-left: 206px;
    }
</style>';

        foreach ($allOptions as $opt) {
            $option_id = $opt->getId();
            $feature = $opt->getCode();

            // Skip features inside folder/folder_v2! the Folder export takes care of these!
            if (!empty($opt->getFolderId())) {
                continue;
            }

            if (Siberian_Exporter::isRegistered($feature)) {
                $label = __($opt->getName()) . ' (' . $opt->getTabbarName() . ')';
                if (Siberian_Exporter::hasOptions($feature)) {
                    $options = ['skip' => __('Do not export')] + Siberian_Exporter::getOptions($feature);

                    $el = $this->addSimpleSelect($option_id, $label, $options);
                    $el->setOptions([
                        'belongsTo' => 'options',
                        'value' => $option_id
                    ]);
                    $el->setValue('safe');

                    if (in_array($feature, ['folder', 'folder_v2'])) {
                        // Find all subfeatures!
                        $subfeatures = (new Application_Model_Option_Value())
                            ->findAll(
                                [
                                    'folder_id' => $opt->getId()
                                ]
                            );

                        if ($subfeatures->count() > 0) {
                            $subFeaturesGroup = [];
                            foreach ($subfeatures as $subfeature) {
                                $featureCode = $subfeature->getCode();
                                if (Siberian_Exporter::hasOptions($featureCode)) {
                                    $featureOptions = ['skip' => __('Do not export')] +
                                        Siberian_Exporter::getOptions($featureCode);

                                    // Folder export options!
                                    $selectName = 'subfeature_' . $subfeature->getId();
                                    $this
                                        ->addSimpleSelect(
                                            $selectName,
                                            $subfeature->getTabbarName(),
                                            $featureOptions)
                                        ->setValue('safe')
                                        ->setBelongsTo('export_type_feature');

                                    $subFeaturesGroup[] = $selectName;
                                } else {
                                    // We do not display sub-features without options
                                }
                            }

                            if (!empty($subFeaturesGroup)) {
                                $this->groupElements(
                                    'subfeatures_group_' . $opt->getId(),
                                    $subFeaturesGroup);

                                $globalMarkup .= '
<script type="text/javascript">
    $(document).ready(function () {
        $("#options-' . $opt->getId() . '").on("change", function () {
            if ($(this).val() === "all") {
                $("#fieldset-subfeatures_group_' . $opt->getId() . '").show();
            } else {
                $("#fieldset-subfeatures_group_' . $opt->getId() . '").hide();
            }
        });
    });
</script>';

                            }
                        }
                    }
                } else {
                    $el = $this->addSimpleCheckbox($option_id, $label);
                    $el->setOptions([
                        'belongsTo' => 'options',
                        'value' => $option_id
                    ]);
                    $el->setValue(true);
                }

                $el->setNewDesign();

                $elements[] = $el;
            }
        }

        $this->addMarkup($globalMarkup);

    }

    public function addTemplate() {
       $is_template = $this->addSimpleCheckbox('is_template', __('Export as global Template'));
       $is_template->setValue(false);

       $template_preview = $this->addSimpleImage(
           'template_preview',
           __('Template preview'),
           __('Template preview'),
           [
               'width' => 640,
               'height' => 1136
           ]
       );
       $template_preview->addClass('toggle_template');

       $template_name = $this->addSimpleText('template_name', __('Template name'));
       $template_name->addClass('toggle_template');

       $template_version = $this->addSimpleText('template_version', __('Template version'));
       $template_version->addClass('toggle_template');

       $template_description = $this->addSimpleText('template_description', __('Template description'));
       $template_description->addClass('toggle_template');

       $this->addNav('export_selection', 'Export App & Selected features.', false, true);
    }

    public function isTemplate() {
        $this->getElement('template_preview')->setRequired(true);
        $this->getElement('template_name')->setRequired(true);
        $this->getElement('template_version')->setRequired(true);
    }
}