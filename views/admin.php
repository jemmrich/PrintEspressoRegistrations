<style>
    .per-event-select-form{
        margin: 20px 0px;
    }
    
    .per-print-button{
        margin: 10px;
    }

    @media print{
        html body *{
            visability: hidden;
        }
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
                        if($event->ID != $_GET["event_id"])
                            echo "<option value='" . $event->ID . "'>" . date("Y-m-d", strtotime($event->DTT_EVT_start)) . " - " . $event->post_title . "</option>";
                        else
                            echo "<option selected value='" . $event->ID . "'>" . date("Y-m-d", strtotime($event->DTT_EVT_start)) . " - " . $event->post_title . "</option>";
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
