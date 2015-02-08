<style>
    .per-event-select-form{
        margin: 20px 0px;
    }
    .per-registrations {
        width: 100%;
        border: 1px solid black;
        border-spacing: 0px;
    }
    .per-registrations th{
        font-weight: 700;
        text-align: left;
        padding: 10px;
        border-bottom: 1px solid black;
    }
    .per-registrations td{
        padding: 10px;
        border-bottom: 1px solid black;
    }
    .per-registrations tr:last-child td{
        border: 0px;
        font-weight: 700;
    }
    .per-print-button{
        margin: 10px;
    }

    @media print{
        #adminmenuback, #adminmenuwrap, #wpfooter {
            display: none;
        }
        #wpcontent{
            margin: 0;
            padding: 0;
        }
        .per-header{
            display: none;
        }
        .per-event-registrations{
            display: block;
        }
        .per-print-button{
            display: none;
        }
        
    }
</style>



<div class="per-header">
    <h2>Print Event Espresso Registrations</h2>

    <h3>Select Event</h3>
    <p>
        Select the event you wish to print registrations for:
    </p>

    <div class="per-event-select-form">
    <?php if($events != false){ ?>
        <form>
            <input type="hidden" name="page" value="print-registrations" />
            <select name="event_id" method="get">
                <?php
                    foreach($events as $event){
                        echo "<option value='" . $event->ID . "'>" . date("Y-m-d", strtotime($event->post_date)) . " - " . $event->post_title . "</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Go">
        </form>
    <?php }else{ ?>
    There are currently no events in the system.
    <?php } ?>
    </div>

    <hr>
</div>
