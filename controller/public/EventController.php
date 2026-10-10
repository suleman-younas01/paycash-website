<?php
require("config/env.php");
if($route == '/events'){
    $seo = array(
        'title' => 'All Events',
        'description' => 'Biafra Application Forms',
        'keywords' => 'security app'
    );
    $data = $h->table('event')->select()->where('status', '=', 'active')->fetchAll();
    foreach ($data as &$event) {
        $event['event_description'] = base64_decode($event['event_description']);
    }
        echo $twig->render('public/events/all.twig', ['seo'=>$seo,'csrf'=>set_csrf(),'countries' => getCountries(), 'formData' => $data]);
}
if($route == '/event/details/$id'){
    $data = $h->table('event')->select()->where('id', '=', $id)->fetchAll();
    $date =  $data[0]['event_date'];
    $formatteddate = date("F j, Y", strtotime($date));
    $formattedEndDate = date("F j, Y", strtotime($data[0]['event_end_date']));
    $formData = [
        'id' => $data[0]['id'],
        'slug' => $data[0]['slug'],
        'event_image' => $data[0]['event_image'],
        'event_name' => $data[0]['event_name'],
        'event_description' => base64_decode(ucfirst($data[0]['event_description'])),
        'event_ticket_category' => $data[0]['event_ticket_category'],
        'event_price' => $data[0]['event_price'],
        'event_location' => $data[0]['event_location'],
        'date' => $data[0]['event_date'],
        'formatteddate' => $formatteddate,
        'formattedEndDate' => $formattedEndDate,
    ];
    $seo = array(
        'title' => ucfirst($data[0]['event_name']),
        'description' => 'Biafra Application Forms',
        'keywords' => 'security app'
    );
    echo $twig->render('public/events/event_details.twig', ['seo'=>$seo,'csrf'=>set_csrf(),'countries' => getCountries(), 'formData' => $formData]);
}

if($route == '/event/form/$slug'){
    $seo = array(
        'title' => 'APPLICANT’S PERSONAL INFORMATION',
        'description' => 'Biafra Application Forms',
        'keywords' => 'security app'
    );
    $eventData = $h->table('event')->select()->where('slug', '=', $slug)->fetchAll();
        $formData = [
            'table_name' => 'event_form',
            'event_id' => $eventData[0]['id'],
            'event_ticket_category' => $eventData[0]['event_ticket_category'],
            'event_price' => $eventData[0]['event_price'],
            'slug' => $slug,
        ];
        echo $twig->render('public/events/event_form.twig', ['seo'=>$seo,'csrf'=>set_csrf(),'countries' => getCountries(), 'formData' => $formData]);
}
if($route == '/submit/event/form'){
    @$event_slug= $_POST['event_slug'];
    @$table_name= $_POST['table_name'];
    @$fname = $_POST['fname'];
    @$event_id = $_POST['event_id'];
    @$mname= $_POST['mname'];
    @$surname =$_POST['surname'];
    @$email= $_POST['email'];
    @$telephone = $_POST['telephone'];
    @$country_residence= $_POST['country_residence'];
    @$nationality_biafran= $_POST['nationality_biafran'];
    @$nationality= $_POST['nationality'];
    @$attend_bsrc= $_POST['attend_bsrc'];
    @$support_biafra= $_POST['support_biafra'];
    @$ticket_category= $_POST['ticket_category'];
    if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] == UPLOAD_ERR_OK) {

        $id_proof=UploadIMGBYAPI('id_proof','uploads/events/form');
        @$id_proofArr = json_decode($id_proof);
        @$id_proof = $id_proofArr->imageName;

    }else{
        @$id_proof='null';
    }
    if (isset($_FILES['convention_certificate']) && $_FILES['convention_certificate']['error'] == UPLOAD_ERR_OK) {
        $convention_certificate=UploadIMGBYAPI('convention_certificate','uploads/events/form');
        @$convention_certificateArr = json_decode($convention_certificate);
        @$convention_certificate = $convention_certificateArr->imageName;

    }else{
        @$convention_certificate='null';
    }
    $res= $h->insert($table_name)->values(['user_id' => $loginUserId,'event_id' => $event_id,'slug' => $event_slug,'fname' => $fname, 'mname' => $mname, 'surname' => $surname, 'email' => $email,'telephone' => $telephone,
        'country_residence' => $country_residence, 'nationality_biafran' => $nationality_biafran, 'nationality' => $nationality, 'attend_bsrc' => $attend_bsrc, 'id_proof' => $id_proof,'support_biafra' => $support_biafra,'ticket_category' => $ticket_category, 'convention_certificate' => $convention_certificate
    ])->run();
    if($res){
        $lastInsertId = $res;
        echo json_encode(array("statusCode" => "1", "id"=>$lastInsertId ,"slug"=>$event_slug,"ticket_category"=>$ticket_category));
        exit();
    }else{
        echo "0";
        exit();
    }
}
if($route == '/update/event/form/$id'){
    $seo = array(
        'title' => 'APPLICANT’S PERSONAL INFORMATION',
        'description' => 'Biafra Application Forms',
        'keywords' => 'security app'
    );
    $event_form = $h->table('event_form')->select()->where('id', '=', $id)->fetchAll();
    $eventData = $h->table('event')->select()->where('id', '=', $event_form[0]['event_id'])->fetchAll();
    $formData = [
        'table_name' => 'event_form',
        'event_id' => $event_form[0]['event_id'],
        'database_id' => $event_form[0]['id'],
        'ticket_category' => $event_form[0]['ticket_category'],
        'total_price' => $event_form[0]['total_price'],
        'fname' => $event_form[0]['fname'],
        'mname' => $event_form[0]['mname'],
        'surname' => $event_form[0]['surname'],
        'email' => $event_form[0]['email'],
        'telephone' => $event_form[0]['telephone'],
        'country_residence' => $event_form[0]['country_residence'],
        'nationality_biafran' => $event_form[0]['nationality_biafran'],
        'nationality' => $event_form[0]['nationality'],
        'id_proof' => "https://www.biafrasecurityadministration.org/uploads/events/form/" .$event_form[0]['id_proof'],
        'id_proof_check' => $event_form[0]['id_proof'],
        'attend_bsrc' => $event_form[0]['attend_bsrc'],
        'convention_certificate' => "https://www.biafrasecurityadministration.org/uploads/events/form/" .$event_form[0]['convention_certificate'],
        'convention_certificate_check' => $event_form[0]['convention_certificate'],
        'support_biafra' => $event_form[0]['support_biafra'],

    ];
    echo $twig->render('public/events/update_event_form.twig', ['seo'=>$seo,'csrf'=>set_csrf(),'countries' => getCountries(), 'formData' => $formData]);
}
if($route == '/submit/update/event/form'){
    @$database_id= $_POST['database_id'];
    @$table_name= $_POST['table_name'];
    @$fname = $_POST['fname'];
    @$mname= $_POST['mname'];
    @$surname =$_POST['surname'];
    @$email= $_POST['email'];
    @$telephone = $_POST['telephone'];
    @$country_residence= $_POST['country_residence'];
    @$nationality_biafran= $_POST['nationality_biafran'];
    @$nationality= $_POST['nationality'];
    @$attend_bsrc= $_POST['attend_bsrc'];
    @$support_biafra= $_POST['support_biafra'];
    if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] == UPLOAD_ERR_OK) {
        $id_proof=UploadIMGBYAPI('id_proof','uploads/events/form');
        @$id_proofArr = json_decode($id_proof);
        @$id_proof = $id_proofArr->imageName;

    }else{
        @$id_proof='null';
    }
    if (isset($_FILES['convention_certificate']) && $_FILES['convention_certificate']['error'] == UPLOAD_ERR_OK) {
        $convention_certificate=UploadIMGBYAPI('convention_certificate','uploads/events/form');
        @$convention_certificateArr = json_decode($convention_certificate);
        @$convention_certificate = $convention_certificateArr->imageName;

    }else{
        @$convention_certificate='null';
    }
    $res= $h->table($table_name)->update(['fname' => $fname, 'mname' => $mname, 'surname' => $surname, 'email' => $email,'telephone' => $telephone,
        'country_residence' => $country_residence, 'nationality_biafran' => $nationality_biafran, 'nationality' => $nationality, 'attend_bsrc' => $attend_bsrc, 'id_proof' => $id_proof,'support_biafra' => $support_biafra,'convention_certificate' => $convention_certificate
    ])->where('id','=',$database_id)->run();
    $event_form = $h->table('event_form')->select()->where('id', '=', $database_id)->fetchAll();

    if($res){
        if($event_form[0]['payment'] == 'no'){
            $slug=$event_form[0]['slug'];
            $ticket_category=$event_form[0]['ticket_category'];
            echo json_encode(array("statusCode" => "1", "id"=>$database_id ,"slug"=>$slug,"ticket_category"=>$ticket_category));
            exit();
        }else{
            echo json_encode(array("statusCode" => "1", "id"=>'' ,"slug"=>'',"ticket_category"=>''));
            exit();
        }
    }else{
        if($event_form[0]['payment'] == 'no'){
            $slug=$event_form[0]['slug'];
            $ticket_category=$event_form[0]['ticket_category'];
            echo json_encode(array("statusCode" => "0", "id"=>$database_id ,"slug"=>$slug,"ticket_category"=>$ticket_category));
            exit();
        }else{
            echo json_encode(array("statusCode" => "0", "id"=>'' ,"slug"=>'',"ticket_category"=>''));
            exit();
        }
    }
}
if($route == '/events/purchased'){
    $seo = array(
        'title' => 'All Purchased Tickets',
        'description' => 'Biafra Application Forms',
        'keywords' => 'security app'
    );
    $data = $h->table('event_form')->select()->where('user_id', '=', $loginUserId)->orderBy('id', 'desc')->fetchAll();

    echo $twig->render('public/events/purchased_tickets.twig', ['seo'=>$seo,'csrf'=>set_csrf(),'countries' => getCountries(), 'formData' => $data]);
}