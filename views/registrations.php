
<div class="per-event-registrations">
    <h2>Registrations</h2>

    <input type="button" value="Print Report" class="per-print-button" onclick="javascript:window.print();"/>

    <h3><?=$event_name?></h3>
    <h4>Event date: <?=$event_date?></h4>
    
    <table class="per-registrations">
        <tr>
            <th width="50px">Contact ID</th>
            <th width="">Name</th>
            <th width="">Email</th>
            <th width="140px">Reg Date</th>
            <th width="100px">Reg Code</th>
            <th width="100px" style="display: none;">TXN Price</th>
            <th width="100px" style="display: none;">Total Txn</th>
            <th width="100px">Paid</th>
            <th width="100%">Comments</th>
        </tr>
        
        <?php 
        if($attendees != false){
            foreach($attendees as $attendee){
                echo '<tr>';
                echo '  <td>' . $attendee->ATT_ID . '</td>';
                echo '  <td nowrap><a href="admin.php?page=espresso_registrations&action=edit_attendee&post=' . $attendee->ATT_ID . '">' . $attendee->ATT_fname . ' ' . $attendee->ATT_lname . '</a></td>';
                echo '  <td nowrap>' . $attendee->ATT_email . '</td>';
                echo '  <td nowrap>' . $attendee->REG_date . '</td>';
                echo '  <td nowrap>' . $attendee->REG_code . '</td>';
                echo '  <td style="display: none;">$' . number_format($attendee->REG_final_price, 2) . '</td>';
                echo '  <td style="display: none;">$' . number_format($attendee->REG_final_price, 2) . '</td>';
                if(number_format($attendee->TXN_price, 2) == number_format($attendee->REG_final_price, 2))
                    echo '  <td>Paid</td>';
                else
                    echo '  <td>Not Paid</td>';
                echo '  <td>&nbsp;</td>';
                echo '</tr>';
                
            }
            echo '<tr><td colspan="6" align="right">Total Attendees:</td><td>' . sizeof($attendees) . '</td></tr>';
        }else{
            echo '<tr><td colspan="7" align="center">There are currently no registrations.</td></tr>';
        }
        ?>
    </table>
</div>
