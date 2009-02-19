{?Extensions}
    <div id="extensions">
        <h2>Enabled Extensions</h2>
        <p class="information">_{Select an Extension to enable at your site. Core Extensions are protected and can't be disabled.}</p>
        <div class="form">
            {loop Extensions}
                <p class="inline">
                    {!Extension.is_core}
                    <input type="hidden" value="0" name="extension[{Extension.id}][is_enabled]"/>
                    <input id="extension_is_enabled-{Extension.id}" {?Extension.is_enabled}checked="checked"{end} type="checkbox" value="1" name="extension[{Extension.id}][is_enabled]"/>
                    {end}
                    <label {?Extension.is_core}class="core_extension"{end} for="extension_is_enabled-{Extension.id}">
                        {Extension.name}
                        
                        {?Extension.version}
                            <span class="secondary small">, _{version} {Extension.version}</span>
                        {end}
                        
                        {?Extension.is_core}
                            <span class="small">
                                <span class="important">(</span>
                                <span class="secondary">_{core extension}</span>
                                <span class="important">)</span>
                            </span>
                        {end}
                    </label> 
                </p>
            {end}
        </div>
    </div>
{end}
