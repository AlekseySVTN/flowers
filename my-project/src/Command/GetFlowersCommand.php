<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetFlowersCommand extends Command
{
    protected static $defaultName = 'app:get_flowers';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }




        $_SERVER['PANTHER_NO_HEADLESS'] = true;

        try{
            $client = \Symfony\Component\Panther\Client::createChromeClient();
            $crawler = $client->request('GET', 'https://bukedo.ru/cabinet/login/?next=/cabinet/'); // Yes, this
            sleep(1);
            $client->executeScript("$('.content__wrapper').find('form input[name=\'username\']').val('".$_SERVER["LOGIN_FLOWERS_FROM"]."')");
            $client->executeScript("$('.content__wrapper').find('form input[name=\'password\']').val('".$_SERVER["PASS_FLOWERS_FROM"]."')");
            $client->executeScript("$('.content__wrapper').find('.btn.yellow').click();");
            sleep(2);
            $crawler = $client->request('GET', 'https://bukedo.ru/cabinet/orders/?created_from=2019-2-1&created_to=2019-6-24&page_size=500&status=3'); // Yes, this
            sleep(2);

            $script = 'var orders = {};
$(\'.table-lg_orders .modal-link\').each(function(){
var order_id = $(this).find("td:nth-child(2)").text();
var order_date = $(this).find("td:nth-child(3)").text();
var order_address = $(this).find("td:nth-child(4)").text();
var delivery_date = $(this).find("td:nth-child(5)").text();
var price = $(this).find("td:nth-child(7)").text();
	orders[order_id] = {"order_date" :order_date,"order_address":order_address,"delivery_date":delivery_date};
}); return orders;';
            $order_ids = $client->executeScript($script);
            sleep(2);
            $jquery = 'var script = document.createElement(\'script\');
script.src = \'https://code.jquery.com/jquery-1.11.0.min.js\';
script.type = \'text/javascript\';
document.getElementsByTagName(\'head\')[0].appendChild(script);';

            $fp = fopen('/home/aleksey/strom_projects/flowers/flowers/my-project/1.csv', 'w');
            foreach ($order_ids as $order_id=>$order){
                if(!$order_id){
                    continue;
                }

                // TODO проверить на почту заказа и не плохая оценка
                $data = [];
                $data["id"]["name"]  = "номер заказа";
                $data["id"]["value"] = $order_id;
                $data["date"]["name"] = "дата получения заказа";
                $data["date"]["value"] = $order["order_date"];

                $data["delivery_time"]["name"] = "дата доставки и время";
                $data["delivery_time"]["value"] = $order["delivery_date"];
                $data["delivery_address"]["name"] = "адрес доставки";
                $data["delivery_address"]["value"] = $order["order_address"];

                $client->request('GET', "https://bukedo.ru/cabinet/orders/".$order_id."/form/");
                $client->executeScript($jquery);
                sleep(2);
                $data["sum"]["name"] = "стоимость общая";
                $data["sum"]["value"] = $client->executeScript("return $('.sum-row .price strong').text()");
                $data["open_text"]["name"] = "текст открытки";
                $data["open_text"]["value"] = "!!!";//$client->executeScript("return $('.sum-row .price strong').text()");
                $data["phone1"]["name"] = "телефон заказчика";
                $data["phone1"]["value"] = $client->executeScript("return $('.info-block div.user-contact-info').eq(1).find('.phone').text()");
                $data["email1"]["name"] = "почта заказчика";
                $data["email1"]["value"] = $client->executeScript("return $('.info-block div.user-contact-info').eq(1).find('.clearfix > a.common-link_underline').text()");
                $data["fio1"]["name"] = "Имя заказчика";
                $data["fio1"]["value"] = $client->executeScript("return $('.info-block div.user-contact-info').eq(1).find('.name').text() ? $('.info-block div.user-contact-info').eq(1).find('.name').text():'Любимый покупатель'");
                $data["fio0"]["name"] = "Имя получателя";
                $data["fio0"]["value"] = $client->executeScript("return $('.info-block div.user-contact-info').eq(0).find('.name').text()");
                $data["phone0"]["name"] = "телефон получателя";
                $data["phone0"]["value"] = $client->executeScript("return $('.info-block div.user-contact-info').eq(0).find('.phone').text()");
                $data["povod"]["name"] = "Повод";
                $data["povod"]["value"] = "!!!";//$client->executeScript("return $('.info-block div.user-contact-info').eq(0).find('.name').text()");
                $data["payment"]["name"] = "оплата всегда выбираю на расчетный счет";
                $data["payment"]["value"] = "!!!";
                $data["notify_email"]["name"] = "уведомить о доставке по email";
                $data["notify_email"]["value"] = 0;
                $data["delivery_time_fact"]["name"] = "фактически доставлено - любое время из интервала доставки";
                $data["delivery_time_fact"]["value"] = "!!!";
                $data["man"]["name"] = "Кто принял (Савинова Элина)";
                $data["man"]["value"] = 0;
                $data["from"]["name"] = "откуда о нас узнали - любое";
                $data["from"]["value"] = 0;

$csv_data = [];
                foreach ($data as &$val){
                    $val["value"] = trim($val["value"]);
                    echo $val["name"] ." - ". $val["value"]." \n";
                    $csv_data[] = $val["value"];
                }







                    fputcsv($fp, $csv_data);
                continue;






                $line = readline("ОТправить ?");
                if($line == "yes"){


                    //https://butterfly-flower.ru/admin логин Yulya пароль Yulya1984 . заказы я беру с
                    $client->request('GET', 'https://butterfly-flower.ru/admin'); // Yes, this
                    sleep("1");
                    $client->executeScript("$('#auth_form input[name=\'username\']').val('".$_SERVER["LOGIN_FLOWERS_TO"]."')");
                    $client->executeScript("$('#auth_form input[name=\'password\']').val('".$_SERVER["PASS_FLOWERS_TO"]."')");
                    $client->executeScript("$('#auth_form button').click();");
                    sleep("2");
                    $client->executeScript("$(\".dashboard-content a:contains(Регистрация заказов)\")[0].click()");
                    sleep("4");
                    $client->executeScript("angular.element(document.querySelector('.glyphicon-plus')).click();");
                    $client->executeScript("angular.element(document.querySelector('#order_id')).val('".$data['id']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#channel_id')).val(".$data['man']['value'].");");
                    $client->executeScript("angular.element(document.querySelector('#buket_price')).val('".$data['sum']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#customer_phone')).val('".$data['phone1']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#customer_email')).val('".$data['email1']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#customer_name')).val('".$data['fio1']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#recipient_phone')).val('".$data['phone0']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#recipient_name')).val('".$data['fio0']['value']."');");
                    $client->executeScript("angular.element(document.querySelector('#reference_id')).val(".$data['from']['value'].");");
                    $client->executeScript("angular.element(document.querySelector('#notice_channel_id')).val(".$data['notify_email']['value'].");");

                    $line = readline("Сохранить с такими данными?");
                }else{
                    return;
                }
                /*
                 * номер заказа
        дату получения заказа
        кто поинял ( савинова элина)
        стоимомть общую (Вам..)
        текст открытки
        состав!!!!!
        телефон заказчика, почта заказчика
        имя, если нет, то пишу Любимый покупатель.
        имя получателя
        телефон
        повод невсегда заполняю
        дата доставки и время
        адрес доставки
        оплата всегда выбираю на расчетный счет
        откуда о нас узнали - любое
        уведомить о доставке по email
        фактически доставлено - любое время из интервала доставки
                 * */
                sleep(15);
                return;
            }
            fclose($fp);


// website is 100% in JavaScript

        }catch (\Exception $exception){
            echo $exception->getMessage();
        }


        $io->success('Процесс завершен;');

    }
}
