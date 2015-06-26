<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Reservations</h1>
        </div>
    </div>
    <div class="row row-top-buffer">
        <div class="col-sm-12">
            <table id="admin_products_list" class="table table-striped table-responsive table-hover table-bordered text-center">
                <thead>
                    <tr>
                        <th class="col-sm-2">Property</th>
                        <th class="col-sm-2">Customer</th>
                        <th class="col-sm-2">Check In</th>
                        <th class="col-sm-1">Length</th>
                        <th class="col-sm-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if(!$data->reservations->isEmpty())
                    {
                        foreach($data->reservations as $reservation)
                        {
                            //Format customer name and address.
                            $customerName = $reservation->customer->first_name . ' ' . $reservation->customer->last_name . '<br />';
                            $customerAddress = $reservation->customer->addr_street_1 . '<br />' . PHP_EOL;
                            $customerAddress .= !empty($reservation->customer->addr_street_2) ? $reservation->customer->addr_street_2 . '<br />' . PHP_EOL : null;
                            $customerAddress .= $reservation->customer->addr_city . ', ' . $reservation->customer->addr_state . ' ' . $reservation->customer->addr_zip;

                            $htmlOut = '<tr>' . PHP_EOL;
                            $htmlOut .= '<td>' . PHP_EOL;
                            $htmlOut .= $reservation->property->name . PHP_EOL;
                            $htmlOut .= '</td>' . PHP_EOL;
                            $htmlOut .= '<td>' . PHP_EOL;
                            $htmlOut .= $customerName . PHP_EOL;
                            $htmlOut .= $customerAddress . PHP_EOL;
                            $htmlOut .= '</td>' . PHP_EOL;
                            $htmlOut .= '<td>' . PHP_EOL;
                            $htmlOut .= $reservation->check_in . PHP_EOL;
                            $htmlOut .= '</td>' . PHP_EOL;
                            $htmlOut .= '<td>' . PHP_EOL;
                            $htmlOut .= $reservation->getLengthOfStay() . ($reservation->getLengthOfStay() > 1 ? ' Days' : ' Day') . PHP_EOL;
                            $htmlOut .= '</td>' . PHP_EOL;
                            $htmlOut .= '<td>' . PHP_EOL;
                            $htmlOut .= '<form class="update-reservation" method="POST" action="/admin/reservations/' . $reservation->rid . '">' . PHP_EOL;
                            $htmlOut .= '<input type="hidden" name="_METHOD" value="PUT"/>' . PHP_EOL;
                            $htmlOut .= '<input type="hidden" name="_CSRF" value="' . $data->csrfToken . '"/>' . PHP_EOL;
                            $htmlOut .= '<div class="col-sm-8">' . PHP_EOL;
                            $htmlOut .= '<select name="reservation-status" class="form-control required" required>' . PHP_EOL;
                            $htmlOut .= '<option ' . (intval($reservation->status) === 0 ? 'selected="selected"' : null) . ' value="0">Pending</option>' . PHP_EOL;
                            $htmlOut .= '<option ' . (intval($reservation->status) === 1 ? 'selected="selected"' : null) . ' value="1">Confirmed</option>' . PHP_EOL;
                            $htmlOut .= '<option ' . (intval($reservation->status) === 2 ? 'selected="selected"' : null) . ' value="2">Paid</option>' . PHP_EOL;
                            $htmlOut .= '<option ' . (intval($reservation->status) === 3 ? 'selected="selected"' : null) . ' value="3">Cancelled</option>' . PHP_EOL;
                            $htmlOut .= '</select>' . PHP_EOL;
                            $htmlOut .= '</div>' . PHP_EOL;
                            $htmlOut .= '<div class="col-sm-4">' . PHP_EOL;
                            $htmlOut .= '<button type="submit" class="btn btn-primary update-btn">Update</button>' . PHP_EOL;
                            $htmlOut .= '</div>' . PHP_EOL;
                            $htmlOut .= '</form>' . PHP_EOL;
                            $htmlOut .= '</td>' . PHP_EOL;
                            $htmlOut .= '</tr>' . PHP_EOL;
                            echo $htmlOut;
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
    include 'includes/footer.php';
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<script>
    /**
     * Handle reservation updates.
     *
     */
    $('form.update-reservation').each(function()
    {
        $(this).submit(function(e)
        {
            var updateBtn = $(this).find('button.update-btn');
            updateBtn.prop('disabled', true);
            var action = $(this).attr('action');
            var data = $(this).serialize();
            $.post(action, data, function(d)
            {
                updateBtn.prop('disabled', false);
                updateBtn.removeClass('btn-primary').addClass('btn-success');
                window.setTimeout(function()
                {
                    updateBtn.removeClass('btn-success').addClass('btn-primary');
                }, 3000);
            });
            e.preventDefault();
        });
    });
</script>
</body>
</html>