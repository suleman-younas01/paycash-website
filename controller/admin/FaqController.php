<?php
require("config/env.php");

if ($route == '/admin/faqs'):

    $seo = array(
        'title' => 'FAQs | Paycash',
        'description' => 'Manage FAQs for your site.',
        'keywords' => 'FAQs, FAQ Management'
    );
    $faqs = $h->table('faqs')->select()->fetchAll();

    echo $twig->render('admin/faqs/add_faqs.twig', ['seo' => $seo, 'faqs' => $faqs, 'csrf' => set_csrf()]);

endif;


if ($route == '/admin/add/faqs'):

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['question']) && isset($_POST['answer'])) {
            $question = $_POST['question'];
            $answer = $_POST['answer'];

            if (empty($question) || empty($answer)) {
                echo "3"; // Required fields are empty
                exit();
            }

            $insert = $h->insert('faqs')->values([
                'question' => $question,
                'answer' => $answer,
            ])->run();

            if ($insert) {
                echo "1";
                exit();
            } else {
                echo "0";
                exit();
            }
        }
    }

endif;


if ($route == '/admin/api/faqs') {
    $domainsSQL = $h->table('faqs')->select();
    $courses = $domainsSQL->fetchAll();
    $domainsCount = $domainsSQL->count();
    $selectedDate = [];

    if ($domainsCount > 0) {
        $srNo = 0;
        foreach ($courses as $row) {
            $selectedDate[] = [
                'srNo' => $srNo = $srNo + 1,
                'id' => $row['id'],
                'question' => ($row['question']),
                'answer' => ($row['answer']),  // HTML tags ko remove karna
            ];
        }
    }
    echo json_encode(['data' => $selectedDate]);
}

if ($route == '/admin/view/edit/faq'):

    if (isset($_POST['edit']) && !empty($_POST['edit'])) {
        $id = $_POST['id'];

        $editQuery = $h->table('faqs')->select()->where('id', '=', $id)->fetchAll();
        echo json_encode($editQuery);
        exit();
    }
endif;


if ($route == '/admin/update/faqs') {
    // Validate that all required fields are not empty
    if (
        !empty($_POST['editQuestion']) &&
        !empty($_POST['id']) &&
        !empty($_POST['editAnswer'])
    ) {
        $editQuestion = $_POST['editQuestion'];
        $id = $_POST['id'];
        $editAnswer =($_POST['editAnswer']);

            // Update with image
            $update = $h->table('faqs')->update([
                'question' => $editQuestion,
                'answer' => $editAnswer
            ])->where('id', '=', $id)->run();

            if ($update) {
                echo "1"; // Success
            } else {
                echo "0"; // Error during insertion
            }
    } else {
        echo "2"; // Validation failed, some fields are empty
        exit();
    }
}

