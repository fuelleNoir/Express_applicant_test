<?php

namespace NamePlugin;

class NameApi {
    public $api_url;

    public function list_vacansies($post, $vid = 0) {
        //Переменная $wpbd не нужна. Также модификатор global для определения переменной в часто вызываемой функции не нужен.
        //Если от значения переменной $post зависит return value, то лучше проверку перенести в самое начало.
        if (!is_object($post)) {
            return false;
        }

        $ret = array();
        $page = 0;
        $found = false;
        //Конструкции, нарушающие последовательность кода, такие как goto вредны, в данном случае заменяем на while
        while(true)
        {
            $params = "status=all&id_user=" . $this->self_get_option('superjob_user_id') . "&with_new_response=0&order_field=date&order_direction=desc&page={$page}&count=100";
            
            //Неизвестно, что возвращает api_send, но в изначальном коде мы сравниваем $res как булевую переменную и одновременно для нее вызываем json_decode
            //Добавим обработчик исключений на уязвимый код
            try
            {
                $res = $this->api_send($this->api_url . '/hr/vacancies/?' . $params);
            }
            catch (Exception $exception)
            {
                echo "Ошибка при вызове функции api-send";
                echo $exception->getMessage();
                return $ret;
            }
            //Переопределяем переменную res, нет необходимости в дополнительной переменной
            $res = json_decode($res);
            //Если с параметром $page не было результатов, то выйдем из цикла
            if (empty($res))
            {
                break;
            }
            else
            {
                //Удаляем условия, дублирующие смысловую нагрузку
                $ret = array_merge($res, $ret);
                if ($vid > 0) // Для конкретной вакансии, иначе возвращаем все
                foreach ($res as $value) {
                    if ($value->id == $vid) {
                        //Если мы ищем 1 значение и возвращаем его, то делаем это через return
                        return $value;
                    }
                }
                $page++;
            }
        }
        //Если мы искали конкретную вакансию, то вернули ее выше по коду, если не нашли, то вернули Null, иначе возвращаем все вакансии (условие vid === 0)
        if ($vid === 0)
        {
            return $ret;
        }
        return NULL;
    }  
    
    //Как я поняла, реализация последующих методов открытая и не является тестовым заданием
    public function api_send() {
        return '';
    }
    public function self_get_option($option_name) {
        return '';
    }
}