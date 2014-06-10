
<table class="wp-list-table widefat sucuriscan-table sucuriscan-table-doubletitle sucuriscan-modifiedfiles">
    <thead>
        <tr>
            <th colspan="3" class="thead-with-button">
                <span>Modified files <em>(inside the content directory)</em></span>

                <form action="%%SUCURI.CurrentURL%%#modified-files" method="post" class="thead-topright-action">
                    <input type="hidden" name="sucuriscan_modified_files" value="%%SUCURI.ModifiedFiles.Nonce%%" />
                    <label>
                        Modified in the last
                        <select name="sucuriscan_last_days" id="sucuriscan_last_days">
                        %%SUCURI.ModifiedFiles.SelectOptions%%
                        </select>
                        days
                    </label>
                </form>
            </th>
        </tr>

        <tr>
            <th>Filepath</th>
            <th>CheckSum</th>
            <th>Modification</th>
        </tr>
    </thead>

    <tbody>
        %%SUCURI.ModifiedFiles.List%%

        <tr class="sucuriscan-%%SUCURI.ModifiedFiles.NoFilesVisibility%%">
            <td colspan="3">
                <em>No files modified in the last %%SUCURI.ModifiedFiles.Days%% days</em>
            </td>
        </tr>
    </tbody>
</table>
