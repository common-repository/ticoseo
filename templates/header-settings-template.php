<?php
/**
 * TicoSEO
 *
 * @package   ticoseo
 * @author    Kamil <kamil.baranek@me.com>
 * @copyright 2023 TicoSEO
 * @license   MIT
 * @link      https://kamilbaranek.com
 */
?>
<p>
    <?php
    /**
     * @see \Ticoseo\App\Backend\OpenAI
     * @var $args
     */
    echo '<hr />';
    echo '
    <table class="table-oai-top">
        <tbody>
        <tr>
        <td>
        <div class="prompt-wrapper">
            <div class="generate-ideas-title">
                <button type="button" class="mybutton button-primary" id="generate-lead-btn"><span class="button__text">Generate</span></button>
                <label for="oaipost_mass_prompt_idea" class="prompt-headline"> Ideas</label>	
            </div>
            <textarea name="oaipost_mass_prompt_idea" id="oaipost_mass_prompt_idea" class="large-text code margin-top-7px" rows="4">' . esc_textarea($args[ "data" ][ "prompt_idea" ]) .'</textarea>
        </div>
        </td>
        <td>
        <div class="prompt-wrapper">
            <i class="c-inline-spinner outline"></i>
            <label for="oaipost_mass_prompt_outline" class="prompt-headline">Generate Outlines</label>
            <textarea name="oaipost_mass_prompt_outline" id="oaipost_mass_prompt_outline" class="large-text code margin-top-14px" rows="4">' . esc_textarea($args[ "data" ][ "prompt_outline" ]) .'</textarea>
        </div>
        </td>
        <td>
        <div class="prompt-wrapper">
            <i class="c-inline-spinner generate"></i>
            <label for="oaipost_mass_prompt_content" class="prompt-headline">Generate Content</label>
            <textarea name="oaipost_mass_prompt_content" id="oaipost_mass_prompt_content" class="large-text code margin-top-14px" rows="4">' . esc_textarea($args[ "data" ][ "prompt_content" ]) .'</textarea>
        </div>
        </td>
        </tr>
        
        <tr>
        <td>
            <table>
            <tbody>
            <tr>
                    <td>
                    <div class="inputWrapper">
                      <label for="model_idea" class="">Model</label>
                        <select name="model_idea" id="model_idea">
                            <option value="text-davinci-003" ' . (($args[ "data" ][ "model_idea" ]=="text-davinci-003")?"selected":"") .'>DaVinci</option>
                            <option value="text-curie-001" ' . (($args[ "data" ][ "model_idea" ]=="text-curie-001")?"selected":"") .'>Curie</option>
                            <option value="text-babbage-001" ' . (($args[ "data" ][ "model_idea" ]=="text-babbage-001")?"selected":"") .'>Babbage</option>
                            <option value="text-ada-001" ' . (($args[ "data" ][ "model_idea" ]=="text-ada-001")?"selected":"") .'>Ada</option>
                        </select>
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper margin-left-7px">
                      <label for="temperature_idea" class="margin-left-7px">Temp</label>
                        <input type="number" name="temperature_idea" id="temperature_idea" min="0" max="1"  value="' . esc_attr($args[ "data" ][ "temp_idea" ]) .'" class="temperature">
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper margin-left-7px">
                      <label for="fpenalty_idea" class="margin-left-7px">F. Penalty</label>
                        <input type="number" name="fpenalty_idea" id="fpenalty_idea" min="0" max="1"  value="' . esc_attr($args[ "data" ][ "freq_idea" ]) .'" class="temperature" disabled>
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper margin-left-7px">
                      <label for="maxtokens_idea" class="margin-left-7px">Max Tokens</label>
                        <input type="number" name="maxtokens_idea" id="maxtokens_idea" min="0" max="4096" value="' . esc_attr($args[ "data" ][ "maxtokens_idea" ]) .'" class="temperature">
                    </div>
                    </td>
                    
            </tr>
            </tbody>
            </table>
        </td>
        
        <td>
            <table>
            <tbody>
            <tr>
                    <td>
                    <div class="inputWrapper">
                      <label for="model_outline" class="">Model</label>
                        <select name="model_outline" id="model_outline">
                            <option value="text-davinci-003" ' . (($args[ "data" ][ "model_outline" ]=="text-davinci-003")?"selected":"") .'>DaVinci</option>
                            <option value="text-curie-001" ' . (($args[ "data" ][ "model_outline" ]=="text-curie-001")?"selected":"") .'>Curie</option>
                            <option value="text-babbage-001" ' . (($args[ "data" ][ "model_outline" ]=="text-babbage-001")?"selected":"") .'>Babbage</option>
                            <option value="text-ada-001" ' . (($args[ "data" ][ "model_outline" ]=="text-ada-001")?"selected":"") .'>Ada</option>
                        </select>
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper margin-left-7px">
                      <label for="temperature_outline" class="margin-left-7px">Temp</label>
                        <input type="number" name="temperature_outline" id="temperature_outline" min="0" max="1" value="' . esc_attr($args[ "data" ][ "temp_outline" ]) .'" class="temperature">
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper margin-left-7px">
                      <label for="fpenalty_outline" class="margin-left-7px">F. Penalty</label>
                        <input type="number" name="fpenalty_outline" id="fpenalty_outline" min="0" max="1"  value="' . esc_attr($args[ "data" ][ "freq_outline" ]) .'" class="temperature" disabled>
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper margin-left-7px">
                      <label for="maxtokens_outline" class="margin-left-7px">Max Tokens</label>
                        <input type="number" name="maxtokens_outline" id="maxtokens_outline" min="0" max="4096" value="' . esc_attr($args[ "data" ][ "maxtokens_outline" ]) .'" class="temperature">
                    </div>
                    </td>
                    
            </tr>
            </tbody>
            </table>
        </td>
        
        <td>
            <table>
            <tbody>
            <tr>
                    <td>
                    <div class="inputWrapper">
                      <label for="model_content" class="">Model</label>
                        <select name="model_content" id="model_content">
                            <option value="text-davinci-003" ' . (($args[ "data" ][ "model_content" ]=="text-davinci-003")?"selected":"") .'>DaVinci</option>
                            <option value="text-curie-001" ' . (($args[ "data" ][ "model_content" ]=="text-curie-001")?"selected":"") .'>Curie</option>
                            <option value="text-babbage-001" ' . (($args[ "data" ][ "model_content" ]=="text-babbage-001")?"selected":"") .'>Babbage</option>
                            <option value="text-ada-001" ' . (($args[ "data" ][ "model_content" ]=="text-ada-001")?"selected":"") .'>Ada</option>
                       </select>
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper">
                      <label for="temperature_content">Temp</label>
                        <input type="number" name="temperature_content" id="temperature_content" min="0" max="1"  value="' . esc_attr($args[ "data" ][ "temp_content" ]) .'" class="temperature">
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper">
                      <label for="fpenalty_content">F. Penalty</label>
                        <input type="number" name="fpenalty_content" id="fpenalty_content" min="0" max="1"  value="' . esc_attr($args[ "data" ][ "freq_content" ]) .'" class="temperature" disabled>
                    </div>
                    </td>
                    
                    <td>
                    <div class="inputWrapper">
                      <label for="maxtokens_content">Max Tokens</label>
                        <input type="number" name="maxtokens_content" id="maxtokens_content" min="0" max="4096" value="' . esc_attr($args[ "data" ][ "maxtokens_content" ]) .'" class="temperature">
                    </div>
                    </td>
                    
            </tr>
            </tbody>
            </table>
        </td>
        
        </tr>
        
        
        </tbody>
    </table>
';
    echo '';
    echo '<hr />';

    ?>
</p>
<div id="result">

</div>
