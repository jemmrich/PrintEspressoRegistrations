
<div class="per-event-registrations">
    <h2>Registrations</h2>

    <input type="button" value="Print Report" class="per-print-button" onclick="javascript:window.print();"/> 
    <input type="button" value="Save PDF" class="per-pdf-button" onclick="window.location = 'admin.php?page=print-registrations&action=getpdf&id=<?=$event_id?>'"/>

    <?=$report_html?>
</div>
