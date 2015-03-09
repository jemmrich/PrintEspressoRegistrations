<h3><?=$event_name?></h3>
<h4>Event date: <?=date('Y-m-d', strtotime($event_date))?></h4>

<table class="per-registrations">
    <tr>
        <th width="150px">Name</th>
        <th width="100px">Phone</th>
        <th width="100px">Paid</th>
        <th width="">Comments</th>
    </tr>
    
    <?php 
    if($attendees != false){
        foreach($attendees as $attendee){
            echo '<tr>';
            echo '  <td><a href="admin.php?page=espresso_registrations&action=edit_attendee&post=' . $attendee->ATT_ID . '">' . $attendee->ATT_fname . ' ' . $attendee->ATT_lname . '</a></td>';
            echo '  <td>' . $attendee->ATT_phone . '</td>';
            if($attendee->TXN_paid >= $attendee->TXN_total)
                echo '  <td>Paid</td>';
            else
                echo '  <td>Not Paid</td>';
            echo '  <td>&nbsp;</td>';
            echo '</tr>';
            
        }
        echo '<tr><td colspan="3" align="right">Total Attendees:</td><td>' . sizeof($attendees) . '</td></tr>';
    }else{
        echo '<tr><td colspan="4" align="center">There are currently no registrations.</td></tr>';
    }
    ?>
</table>
