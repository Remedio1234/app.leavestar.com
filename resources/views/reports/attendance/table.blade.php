<?php if(count($data) > 0): $n = 0; ?>
    <?php foreach ($data as $key => $row) : $n++?>
        <tr>
            <td><?php echo $n ?></td>
            <td><?php echo $row->emp_name ?></td>
            <td><?php echo $row->leave_name ?></td>
            <td>
                <?php 
                if($row->balance >= 3600){
                    $h =   floor($row->balance / 3600);
                    if($h > 0) 
                        echo $h .' hr' . ($h > 1 ? 's' : '');
                    else 
                        echo 0 . 'hr';
                } 
                else if($row->balance >= 60 && $row->balance < 3600){
                    $m =  floor(($row->balance / 60) % 60);
                    if($m > 0)
                        echo $m . ' min' . ($m > 0 ? 's' : '');
                    else 
                        echo $m . ' min';
                }
                 else {
                    echo 0;
                }
             ?>
            </td>
            <td>
                <?php 
                if($row->taken >= 3600){
                    $h =   floor($row->taken / 3600);
                    if($h > 0) 
                        echo $h .' hr' . ($h > 1 ? 's' : '');
                    else 
                        echo 0 . 'hr';
                } 
                else if($row->taken >= 60 && $row->taken < 3600){
                    $m =  floor(($row->taken / 60) % 60);
                    if($m > 0)
                        echo $m . ' min' . ($m > 0 ? 's' : '');
                    else 
                        echo $m . ' min';
                }
                 else {
                    echo 0;
                }
             ?>
            </td>
        </tr>
    <?php endforeach ?>
<?php else: ?>
    <tr>
        <td colSpan="5">No records found.</td>
    </tr>
    <?php endif ?>