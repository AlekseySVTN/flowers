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
            $crawler = $client->request('GET', 'https://bukedo.ru/cabinet/orders/?created_from=2019-2-1&created_to=2019-6-24&page_size=1&status=3'); // Yes, this
            sleep(2);

            $script = 'var orders = [];
$(\'.table-lg_orders .modal-link\').each(function(){
	orders.push($(this).find("td:nth-child(2)").text());
}); return orders;';
            $order_ids = $client->executeScript($script);

            $jquery = 'var script = document.createElement(\'script\');
script.src = \'https://code.jquery.com/jquery-1.11.0.min.js\';
script.type = \'text/javascript\';
document.getElementsByTagName(\'head\')[0].appendChild(script);';

            foreach ($order_ids as $order_id){
                if(!$order_id){
                    continue;
                }
                $client->request('GET', "https://bukedo.ru/cabinet/orders/".$order_id."/form/");
                $client->executeScript($jquery);
                sleep(2);
                // TODO проверить на почту заказа и не плохая оценка
                $data = [];
                $data["id"]["name"]  = "номер заказа";
                $data["id"]["value"] = $client->executeScript("return $('.modal__header h2').html()");
                $data["date"]["name"] = "дата получения заказа";
                $data["date"]["value"] = $client->executeScript("return $('.modal__header h2').html()");
                $data["sum"]["name"] = "стоимость общая";
                $data["sum"]["value"] = $client->executeScript("return $('.sum-row .price strong').html()");
                $data["open_text"]["name"] = "текст открытки";
                $data["open_text"]["value"] = $client->executeScript("return $('.sum-row .price strong').html()");
                $data["phone"]["name"] = "телефон заказчика";
                $data["phone"]["value"] = $client->executeScript("return $('.user-contact-info .phone a').html()");
                $data["email"]["name"] = "почта заказчика";
                $data["email"]["value"] = $client->executeScript("return $('.sum-row .price strong').html()");
                $data["fio"]["name"] = "Имя покупателя";
                $data["fio"]["value"] = $client->executeScript("return $('.sum-row .price strong').html()");

                var_dump($data);

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
                    sleep("2");
                    $client->executeScript("angular.element(document.querySelector('.glyphicon-plus')).click();");
                    $client->executeScript("angular.element(document.querySelector('#order_id')).val('".$data['id']['value']."');");
                }else{
                    return;
                }
                /*
                 * номер заказа
        дату получения заказа
        кто поинял ( савинова элина)
        стоимомть общую (Вам..)
        текст открытки
        состав
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


// website is 100% in JavaScript

        }catch (\Exception $exception){
            echo $exception->getMessage();
        }


        $io->success('Процесс завершен;');

    }
}
