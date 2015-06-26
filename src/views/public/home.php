<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row row-top-buffer">
        <?php
            if(!$data->properties->isEmpty())
            {
                $i = 0;
                foreach($data->properties as $property)
                {
                    $rate = $property->getFormattedRate();
                    $image = ($property->images->first()) ? $property->images->first()->image_full_path : 'http://placehold.it/508x348';
                    $propertyHtml = '<div class="col-sm-4">' . PHP_EOL;
                    $propertyHtml .= '<div class="thumbnail">' . PHP_EOL;
                    $propertyHtml .= '<a href="/properties/' . $property->pid . '">' . PHP_EOL;
                    $propertyHtml .= '<img src="' . $image . '" alt="Vacaction home photo">' . PHP_EOL;
                    $propertyHtml .= '</a>' . PHP_EOL;
                    $propertyHtml .= '<div class="caption">' . PHP_EOL;
                    $propertyHtml .=  "<h3 class=\"inline title\">$property->name</h3>" . PHP_EOL;
                    $propertyHtml .=  "<h4 class=\"inline rate pull-right\">$rate</h4>" . PHP_EOL;
                    $propertyHtml .=  '<div class="clearfix"></div>' . PHP_EOL;
                    $propertyHtml .= $property->short_desc . PHP_EOL;
                    $propertyHtml .= '<p>' . PHP_EOL;
                    $propertyHtml .= '<a href="/properties/' . $property->pid . '" class="btn btn-primary text-right" role="button">View Details</a>' . PHP_EOL;
                    $propertyHtml .= '</p>' . PHP_EOL;
                    $propertyHtml .= '</div>' . PHP_EOL;
                    $propertyHtml .= '</div>' . PHP_EOL;
                    $propertyHtml .= '</div>' . PHP_EOL;
                    echo $propertyHtml;
                    //Start a new Bootstrap row after every 3rd result.
                    if($i === 2)
                    {
                        $i = 0;
                        $rowHtml = '</div>' . PHP_EOL;
                        $rowHtml .= '<div class="row">';
                        echo $rowHtml;
                    }else {
                        $i++;
                    }
                }
            }
        ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>