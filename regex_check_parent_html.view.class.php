<?php



class regex_check_parent_view_html extends regex_check_parent_view
{
    protected $result_wrapper_class = 'single-sample';
    public function get_output()
    {
        $results = '';
        $pairs = '';
        $extra_tabs = '';
        $output = '';
        $regexes = $this->model->get_prop('regexes');
        $sample_len_cls = '';
        $matched_len_cls = '';
        $regex_delim_cls = '';
        $active = '';

        $show_output = false;

        if ($this->model->get_prop('dummy')) {
            $pairs .= $this->child_view->get_regex_fieldset_item(0, $regexes[0]);
            $active = 'sample';
        } else {
            if ($this->model->get_prop('test_only') === false && $this->model->get_prop('output_is_different')) {
                $show_output = true;
            }

            for ($a = 0; $a < count($regexes); $a += 1) {
                $pairs .= $this->child_view->get_regex_fieldset_item(
                    $a, $regexes[$a]
                );
                $results .= $this->get_output_results(
                    $this->child_view->format_report($regexes[$a])
                );
            }
            if ($results !== '') {
                $active = '';
                if ($show_output !== true) {
                    $active_cls = ' active in';
                    $active_cls_ = ' class="active"';
                } else {
                    $active_cls = '';
                    $active_cls_ = '';
                }

                $extra_tabs .= '
                    <li'.$active_cls_.'>'.
                        '<a href="#results" data-toggle="tab" id="results-tab">'.
                            'Results'.
                        '</a>'.
                    '</li>';
                $results = '
                <section id="results" class="tab-pane fade'.$active_cls.' results">
                    <legend>Results</legend>
                    <ol class="'.$this->result_wrapper_class.'">'.$results.'
                    </ol>
                </section>
';
            } else {
                $active = 'regex';
            }

            if ($show_output) {
                $active = '';
                $extra_tabs .= '
                <li class="active">'.
                    '<a href="#output" data-toggle="tab" id="output-tab">Output</a>'.
                '</li>';
                $output = '
                <fieldset id="output" class="tab-pane fade active in">
                    <legend>Replacement</legend>
                    <textarea id="replace" name="replace" readonly="readonly">'.
                        htmlspecialchars($this->model->get_prop('output')).
                    '</textarea>
                </fieldset>
';
            }
        }

        if ($this->model->get_prop('ws_trim_action_after')) {
            $ws_trim_true = ' checked="checked"';
            $ws_trim_false = '';
        } else {
            $ws_trim_true = '';
            $ws_trim_false = ' checked="checked"';
        }

        if ($this->model->get_prop('ws_trim')) {
            $ws_trim_cb = ' checked="checked"';
            $ws_trim_label_cls = '';
        } else {
            $ws_trim_cb = '';
            $ws_trim_true .= ' disabled="disabled"';
            $ws_trim_false .= ' disabled="disabled"';
            $ws_trim_label_cls = 'disabled';
        }

        if ($this->model->get_prop('ws_action_after')) {
            $ws_trim_pos_before = ' checked="checked"';
            $ws_trim_pos_after = '';
        } else {
            $ws_trim_pos_before = '';
            $ws_trim_pos_after = ' checked="checked"';
        }

        if ($this->model->get_prop('split_sample')) {
            $split_sample_cb = ' checked="checked"';
            $split_delim_disabled = '';
            $split_delim_label_cls = '';
        } else {
            $split_delim_disabled = ' disabled="disabled"';
            $split_sample_cb = '';
            $split_delim_label_cls = 'disabled';
        }

        $split_delim = $this->model->get_prop('split_delim');

        switch ($active) {
        case 'sample':
            $active_sample = ' active in';
            $active_sample_cls = ' class="active"';
            $active_regex = '';
            $active_regex_cls = '';
            break;

        case 'regex':
            $active_sample = '';
            $active_sample_cls = '';
            $active_regex = ' active in';
            $active_regex_cls = ' class="active"';
            break;

        default:
            $active_sample = '';
            $active_sample_cls = '';
            $active_regex = '';
            $active_regex_cls = '';
        }

        if (!$this->model->get_prop('sample_len_ok')) {
            $sample_len_cls = ' class="error"';
        }

        if (!$this->model->get_prop('matched_len_ok')) {
            $matched_len_cls = ' class="error"';
        }
        if (!$this->model->get_prop('regex_delim_ok')) {
            $regex_delim_cls = ' class="error"';
        }

        $find = array(
             '{{REQUEST_URI}}'           //  [0] $this->model->get_prop('request_uri')
            ,'{{PAIRS}}'                 //  [1] $pairs
            ,'{{RESULTS}}'               //  [2] $results
            ,'{{EXTRA_TABS}}'            //  [3] $extra_tabs
            ,'{{SAMPLE}}'                //  [4] $this->model->get_prop('sample')
            ,'{{OUTPUT}}'                //  [5] $output
            ,'{{ACTIVE_SAMPLE}}'         //  [6]$active_sample
            ,'{{ACTIVE_SAMPLE_CLS}}'     //  [7] $active_sample_cls
            ,'{{ACTIVE_REGEX}}'          //  [8] $active_regex
            ,'{{ACTIVE_REGEX_CLS}}'      //  [9] $active_regex_cls
            ,'{{SPLIT_SAMPLE_CB}}'       // [10]$split_sample_cb
            ,'{{SPLIT_DELIM}}'           // [11] $split_delim
            ,'{{SPLIT_DELIM_LABEL_CLS}}' // [12] $split_delim_disabled
            ,'{{SPLIT_DELIM_DISABLED}}'  // [13] $split_delim_label_cls
            ,'{{WS_TRIM_CB}}'            // [14] $ws_trim_cb
            ,'{{WS_TRIM_LABEL_CLS}}'     // [15] $ws_trim_true_label_cls
            ,'{{WS_TRIM_TRUE}}'          // [16] $ws_trim_true
            ,'{{WS_TRIM_FALSE}}'         // [17] $ws_trim_false
            ,'{{SAMPLE_LEN}}'            // [18] $this->model->get_static_prop('sample_len')
            ,'{{SAMPLE_LEN_CLS}}'        // [19] $sample_len_cls
            ,'{{MATCHED_LEN}}'           // [20] $this->model->get_static_prop('matched_len')
            ,'{{MATCHED_LEN_CLS}}'       // [21] $matched_len_cls
            ,'{{REGEX_DELIM}}'           // [22] $this->model->get_static_prop('regex_delim')
            ,'{{REGEX_DELIM_CLS}}'       // [23] $regex_delim_cls
            ,'{{WS_TRIM_POS_BEFORE}}'    // [24] $ws_trim_pos_before
            ,'{{WS_TRIM_POS_AFTER}}'     // [25] $ws_trim_pos_after
        );

        $replace = array(
             $this->model->get_prop('request_uri') // [0] 'REQUEST_URI'
            ,$pairs                      //  [1] 'PAIRS'
            ,$results                    //  [2] 'RESULTS'
            ,$extra_tabs                 //  [3] 'EXTRA_TABS'
            ,htmlspecialchars($this->model->get_prop('sample')) // [4] 'SAMPLE'
            ,$output                     //  [5] OUTPUT
            ,$active_sample              //  [6] ACTIVE_SAMPLE
            ,$active_sample_cls          //  [7] ACTIVE_SAMPLE_CLS
            ,$active_regex               //  [8] ACTIVE_REGEX
            ,$active_regex_cls           //  [9] ACTIVE_REGEX_CLS
            ,$split_sample_cb            // [10] SPLIT_SAMPLE_CB
            ,$split_delim                // [11] SPLIT_DELIM
            ,$split_delim_label_cls      // [12] SPLIT_DELIM_DISABLED
            ,$split_delim_disabled       // [13] SPLIT_DELIM_LABEL_CLS
            ,$ws_trim_cb                 // [14] WS_TRIM_CB
            ,$ws_trim_label_cls          // [15] WS_TRIM_TRUE_LABEL_CLS
            ,$ws_trim_true               // [16] WS_TRIM_TRUE
            ,$ws_trim_false              // [17] WS_TRIM_FALSE
            ,$this->model->get_prop('sample_len') // [18] SAMPLE_LEN
            ,$sample_len_cls             // [19] SAMPLE_LEN_CLS
            ,$this->model->get_prop('matched_len') // [19] MATCHED_LEN
            ,$matched_len_cls            // [21] MATCHED_LEN_CLS
            ,$this->model->get_prop('regex_delim') // [20] DELIM_CLOSE
            ,$regex_delim_cls            // [23] REGEX_DELIM_CLS
            ,$ws_trim_pos_before         // [24] WS_TRIM_POS_BEFORE
            ,$ws_trim_pos_after          // [25] WS_TRIM_POS_AFTER
        );

        // debug('$ws_trim_pos_before', $ws_trim_pos_before, $find, $replace);
        return str_replace(
            $find,
            $replace,
            file_get_contents('regex_check_template.html')
        );
    }

    protected function get_output_results( $input )
    {
        return $input;
    }
}

class regex_check_parent_view_html_multi extends regex_check_parent_view_html
{

    protected $result_wrapper_class = 'multi-sample';

    protected function get_output_results( $input )
    {
        return '
                <li>
                    <ol>'.$input.'
                    </ol>
                </li>';
    }
}
