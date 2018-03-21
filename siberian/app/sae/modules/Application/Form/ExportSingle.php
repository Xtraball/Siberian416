<?php
/**
 * Class Application_Form_ExportSingle
 */
class Application_Form_ExportSingle extends Siberian_Form_Abstract
{

    public function init()
    {
        parent::init();

        $this->setAttrib('id', 'form-application-exportsingle');

        // Create
        self::addClass("create", $this);
    }

    public function setExportOptions($option)
    {
        $optionCode = $option->getCode();

        if (Siberian_Exporter::hasOptions($optionCode)) {
            $options = Siberian_Exporter::getOptions($optionCode);
        } else {
            $options = [
                'all' => __('Export all data')
            ];
        }

        // Folder export options!
        $exportOptions = $this->addSimpleSelect('export_type', __('Option'), $options);

        // Find all subfeatures!
        $subfeatures = (new Application_Model_Option_Value())
            ->findAll(
                [
                    'folder_id' => $option->getId()
                ]
            );

        if ($subfeatures->count() > 0) {
            $subFeaturesGroup = [];
            foreach ($subfeatures as $subfeature) {
                $featureCode = $subfeature->getCode();
                if (Siberian_Exporter::hasOptions($featureCode)) {
                    $featureOptions = Siberian_Exporter::getOptions($featureCode);

                    // Folder export options!
                    $selectName = $subfeature->getId();
                    $this
                        ->addSimpleSelect(
                            $selectName,
                            $subfeature->getTabbarName(),
                            $featureOptions)
                        ->setBelongsTo('export_type_feature');
                    $subFeaturesGroup[] = $selectName;
                } else {
                    // We do not display sub-features without options
                }
            }

            if (!empty($subFeaturesGroup)) {
                $this->groupElements(
                    'subfeatures_group',
                    $subFeaturesGroup,
                    __('Subsequent features, only features with export options are listed below.'));
            }
        }


        $js = '
<script type="text/javascript">
    $(document).ready(function () {
        $("#export_type").on("change", function () {
            if ($(this).val() === "all") {
                $("#fieldset-subfeatures_group").show();
            } else {
                $("#fieldset-subfeatures_group").hide();
            }
        });
    });
</script>';

        $this->addMarkup($js);

        $submit = $this->addSubmit(__('Export'), 'export_button');

        $submit->addClass('pull-right');

    }
}