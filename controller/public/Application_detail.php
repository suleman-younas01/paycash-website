<?php
require("config/env.php");
if(!empty($tableName)){
    $seo = array(
        'title' => 'Home | Biafra Security App',
        'description' => 'Biafra',
        'keywords' => 'Biafra security app'
    );
    $data = $h->table($tableName)->select()->where('user_id', '=', $loginUserId)->fetchAll();
    $permissions= $h->table('permissions')->select()->where('table_name','=',$tableName)->fetchAll();
    if($data) {
        $formData = [
            'table_name' => $tableName,
            'database_id' => $data[0]['id'],
            'payment' => $data[0]['payment'],
            'kyc_status' => $data[0]['kyc_verification'],
            'status' => $data[0]['status'],
        ];
        echo $twig->render('public/application_detail.twig', ['seo'=>$seo,'formData'=>$formData,'permissions'=>$permissions,'csrf'=>set_csrf()]);

    }
//    $tableName
}
if($route == '/applications/accepted'){
    $seo = array(
        'title' => 'Accepted Application',
        'description' => 'Biafra',
        'keywords' => 'Biafra security app'
    );

    $permissions = $h->table('permissions')->select()->where('status', '=', 'active')->orderBy('type', 'asc')->fetchAll();

    $completedForms = [];

    foreach ($permissions as $permission) {
        $formTableName = $permission['table_name'];
        $formData = $h->table($formTableName)->select()
            ->where('user_id', '=', $loginUserId)
            ->where('status', '=', 'accepted')
            ->fetchAll();

        if ($formData) {
            foreach ($formData as $form) {
                $completedForms[] = [
                    'form_name' => $permission['form_name'],
                    'type' => $permission['type'],
                    'des' => $permission['des'],
                    'kyc_verification' => $permission['kyc_verification'],
                    'price' => $permission['price'],
                    'status' => $form['status'],
                    'kyc_status' => $form['kyc_verification'],
                    'payment' => $form['payment'],
                    'table_name' => $formTableName,
                ];
            }
        }
    }
    echo $twig->render('public/completed_applications.twig', ['seo' => $seo, 'completedForms' => $completedForms, 'csrf' => set_csrf()]);
    //    $tableName
}