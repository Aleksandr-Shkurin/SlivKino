<?php
namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Session;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [];
        foreach (['триллер','детектив','драма','приключения','мультфильм','семейный','комедия','фантастика'] as $name) {
            $genres[$name] = Genre::firstOrCreate(['name' => $name]);
        }

        $cinema1 = Cinema::create(['name'=>'Сливкино Плаза','name_en'=>'Slivkino Plaza','address'=>'г. Москва, ул. Тверская, 15','phone'=>'+7 (495) 123-45-67','email'=>'plaza@slivkino.ru','working_hours'=>'Ежедневно с 09:00 до 02:00','description'=>'Современный мультиплекс в центре города.','features'=>['IMAX','VIP зал','3D','Кафе','Парковка','Dolby Atmos']]);
        $cinema2 = Cinema::create(['name'=>'Сливкино Молл','name_en'=>'Slivkino Mall','address'=>'г. Москва, пр-т Вернадского, 6','phone'=>'+7 (495) 234-56-78','email'=>'mall@slivkino.ru','working_hours'=>'Ежедневно с 10:00 до 01:00','description'=>'Кинотеатр в крупном торговом центре.','features'=>['Кресла-реклайнеры','3D','Детские сеансы','Парковка','Кафе']]);
        $cinema3 = Cinema::create(['name'=>'Сливкино Океан','name_en'=>'Slivkino Ocean','address'=>'г. Санкт-Петербург, Невский пр-т, 88','phone'=>'+7 (812) 345-67-89','email'=>'ocean@slivkino.ru','working_hours'=>'Ежедневно с 09:30 до 02:30','description'=>'Кинотеатр с панорамным экраном и современной акустикой.','features'=>['Панорамный экран','VIP зал','Бар','Dolby']]);

        $halls = [];
        foreach ([[$cinema1,'Зал 1',8,12],[$cinema1,'Зал 2',7,10],[$cinema2,'Зал 3',6,8],[$cinema2,'Зал 4',10,14],[$cinema1,'Зал 5',6,8]] as [$cinema,$name,$rows,$seats]) {
            $halls[] = Hall::create(['cinema_id'=>$cinema->id,'name'=>$name,'rows_count'=>$rows,'seats_per_row'=>$seats,'vip_seats'=>[],'disabled_seats'=>[]]);
        }

        $movieData = [
            ['title'=>'Горничная','title_en'=>'The Housemaid','country'=>'США','year'=>2026,'age_rating'=>'18+','release_date'=>'2026-01-08','end_date'=>'2026-03-20','poster'=>'images/posters/maid.jpg','description'=>'Психологический триллер с Сидни Суини.','full_description'=>'Молодая женщина устраивается горничной в богатый дом, но вскоре обнаруживает, что идеальная семья скрывает мрачные тайны.','director'=>'Пол Верховен','cast'=>['Сидни Суини','Аманда Сейфрид','Мишель Пфайффер'],'duration_minutes'=>118,'coming_soon'=>false,'genres'=>['триллер','детектив','драма']],
            ['title'=>'Прыгуны','title_en'=>'Jumpers','country'=>'США','year'=>2026,'age_rating'=>'6+','release_date'=>'2026-01-15','end_date'=>'2026-03-25','poster'=>'images/posters/foodland.jpg','description'=>'Яркий анимационный фильм о приключениях.','full_description'=>'История о необычном приключении, которое начинается после закрытия супермаркета.','director'=>'Фил Лорд','cast'=>['Крис Пратт','Анна Кендрик','Сэмюэл Л. Джексон'],'duration_minutes'=>95,'coming_soon'=>false,'genres'=>['приключения','мультфильм','семейный','комедия','фантастика']],
            ['title'=>'Чебурашка 2','title_en'=>'Cheburashka 2','country'=>'Россия','year'=>2026,'age_rating'=>'6+','release_date'=>'2026-01-01','end_date'=>'2026-03-30','poster'=>'images/posters/cheburashka2.jpg','description'=>'Продолжение семейной комедии.','full_description'=>'Чебурашка и Гена возвращаются в новых приключениях.','director'=>'Дмитрий Дьяченко','cast'=>['Сергей Гармаш','Ольга Кузьмина','Полина Максимова'],'duration_minutes'=>110,'coming_soon'=>false,'genres'=>['семейный','комедия']],
            ['title'=>'Марти Великолепный','title_en'=>'Marty Magnificent','country'=>'США','year'=>2026,'age_rating'=>'16+','release_date'=>'2026-04-05','end_date'=>'2026-05-10','poster'=>null,'description'=>'Новый фильм с участием популярных актёров.','full_description'=>'История о Марти, который внезапно получает суперспособности.','director'=>'Тайка Вайтити','cast'=>['Джош Бролин','Флоренс Пью','Педро Паскаль'],'duration_minutes'=>125,'coming_soon'=>true,'genres'=>['драма','комедия']],
            ['title'=>'Аватар 3','title_en'=>'Avatar 3','country'=>'США','year'=>2026,'age_rating'=>'12+','release_date'=>'2026-04-19','end_date'=>'2026-06-15','poster'=>null,'description'=>'Продолжение фантастической саги.','full_description'=>'Продолжение приключений на Пандоре.','director'=>'Джеймс Кэмерон','cast'=>['Сэм Уортингтон','Зои Салдана','Сигурни Уивер'],'duration_minutes'=>190,'coming_soon'=>true,'genres'=>['фантастика','приключения']],
        ];
        foreach ($movieData as $data) {
            $movieGenres = $data['genres']; unset($data['genres']);
            $movie = Movie::create($data);
            $movie->genres()->sync(array_map(fn($g) => $genres[$g]->id, $movieGenres));
            if (!$movie->coming_soon) {
                foreach ([['hall'=>$halls[0],'time'=>'10:00','price'=>350],['hall'=>$halls[0],'time'=>'13:30','price'=>400],['hall'=>$halls[1],'time'=>'17:00','price'=>450]] as $slot) {
                    Session::create(['movie_id'=>$movie->id,'hall_id'=>$slot['hall']->id,'starts_at'=>Carbon::parse('2026-11-01 '.$slot['time']),'price'=>$slot['price']]);
                }
            }
        }
    }
}
