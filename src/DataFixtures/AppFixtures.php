<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Language;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private string $projectDir
    ) {}

    public function load(ObjectManager $manager): void
    {
        // ── Langues ──────────────────────────────────────────────────────────
        $languages = [];
        foreach (['Français' => 'fr', 'Anglais' => 'en', 'Espagnol' => 'es', 'Allemand' => 'de', 'Italien' => 'it', 'Japonais' => 'ja', 'Russe' => 'ru', 'Portugais' => 'pt'] as $name => $code) {
            $lang = new Language();
            $lang->setName($name)->setCode($code);
            $manager->persist($lang);
            $languages[$code] = $lang;
        }

        // ── Catégories ───────────────────────────────────────────────────────
        $categories = [];
        foreach (['Roman', 'Science-fiction', 'Histoire', 'Informatique', 'Philosophie', 'Biographie', 'Policier', 'Fantasy', 'Développement personnel', 'Économie', 'Poésie', 'Jeunesse', 'Théâtre', 'Horreur', 'Thriller'] as $name) {
            $cat = new Category();
            $cat->setName($name);
            $manager->persist($cat);
            $categories[$name] = $cat;
        }

        // ── Auteurs ──────────────────────────────────────────────────────────
        $authors = [];
        // Index 0-29 (originaux)
        foreach ([
            ['Victor',       'Hugo'],              // 0
            ['George',       'Orwell'],             // 1
            ['Albert',       'Camus'],              // 2
            ['Frank',        'Herbert'],            // 3
            ['Isaac',        'Asimov'],             // 4
            ['J.R.R.',       'Tolkien'],            // 5
            ['Gustave',      'Flaubert'],           // 6
            ['Marcel',       'Proust'],             // 7
            ['Émile',        'Zola'],               // 8
            ['Stendhal',     ''],                   // 9
            ['Antoine',      'de Saint-Exupéry'],  // 10
            ['Jules',        'Verne'],              // 11
            ['Alexandre',    'Dumas'],              // 12
            ['Guy',          'de Maupassant'],      // 13
            ['Voltaire',     ''],                   // 14
            ['J.K.',         'Rowling'],            // 15
            ['Stephen',      'King'],               // 16
            ['Haruki',       'Murakami'],           // 17
            ['Gabriel',      'García Márquez'],     // 18
            ['Fyodor',       'Dostoïevski'],        // 19
            ['Leo',          'Tolstoï'],            // 20
            ['Franz',        'Kafka'],              // 21
            ['Ernest',       'Hemingway'],          // 22
            ['William',      'Shakespeare'],        // 23
            ['Arthur',       'Conan Doyle'],        // 24
            ['Agatha',       'Christie'],           // 25
            ['Philip',       'K. Dick'],            // 26
            ['Ray',          'Bradbury'],           // 27
            ['John',         'Steinbeck'],          // 28
            ['Charles',      'Dickens'],            // 29
            // Nouveaux auteurs
            ['Honoré',       'de Balzac'],          // 30
            ['Molière',      ''],                   // 31
            ['Jean',         'Racine'],             // 32
            ['Pierre',       'Corneille'],          // 33
            ['Jane',         'Austen'],             // 34
            ['Charlotte',    'Brontë'],             // 35
            ['Emily',        'Brontë'],             // 36
            ['George',       'Eliot'],              // 37
            ['Mark',         'Twain'],              // 38
            ['Herman',       'Melville'],           // 39
            ['Nathaniel',    'Hawthorne'],          // 40
            ['F. Scott',     'Fitzgerald'],         // 41
            ['Harper',       'Lee'],                // 42
            ['J.D.',         'Salinger'],           // 43
            ['William',      'Golding'],            // 44
            ['Oscar',        'Wilde'],              // 45
            ['Mary',         'Shelley'],            // 46
            ['Bram',         'Stoker'],             // 47
            ['Robert Louis', 'Stevenson'],          // 48
            ['Orson Scott',  'Card'],               // 49
            ['Dan',          'Simmons'],            // 50
            ['William',      'Gibson'],             // 51
            ['Douglas',      'Adams'],              // 52
            ['Robert',       'Heinlein'],           // 53
            ['Stanislaw',    'Lem'],                // 54
            ['Ursula K.',    'Le Guin'],            // 55
            ['Margaret',     'Atwood'],             // 56
            ['Daniel',       'Keyes'],              // 57
            ['George R.R.', 'Martin'],              // 58
            ['Patrick',      'Rothfuss'],           // 59
            ['Robert',       'Jordan'],             // 60
            ['Neil',         'Gaiman'],             // 61
            ['Terry',        'Pratchett'],          // 62
            ['Raymond',      'Chandler'],           // 63
            ['Dashiell',     'Hammett'],            // 64
            ['Truman',       'Capote'],             // 65
            ['Stieg',        'Larsson'],            // 66
            ['Gillian',      'Flynn'],              // 67
            ['Dan',          'Brown'],              // 68
            ['Khaled',       'Hosseini'],           // 69
            ['Yann',         'Martel'],             // 70
            ['Cormac',       'McCarthy'],           // 71
            ['Toni',         'Morrison'],           // 72
            ['Salman',       'Rushdie'],            // 73
            ['Kazuo',        'Ishiguro'],           // 74
            ['Mikhail',      'Bulgakov'],           // 75
            ['Boris',        'Pasternak'],          // 76
            ['Paulo',        'Coelho'],             // 77
            ['Simone',       'de Beauvoir'],        // 78
            ['Jean-Paul',    'Sartre'],             // 79
            ['Kurt',         'Vonnegut'],           // 80
            ['Joseph',       'Heller'],             // 81
            ['Ken',          'Kesey'],              // 82
            ['Joseph',       'Heller'],             // 83 (doublon, on garde)
            ['Platon',       ''],                   // 84
            ['Spinoza',      ''],                   // 85
            ['Emmanuel',     'Kant'],               // 86
            ['Georg',        'Hegel'],              // 87
            ['Karl',         'Marx'],               // 88
            ['Martin',       'Heidegger'],          // 89
            ['Dale',         'Carnegie'],           // 90
            ['Napoleon',     'Hill'],               // 91
            ['Stephen',      'Covey'],              // 92
            ['Peter',        'Thiel'],              // 93
            ['Eric',         'Ries'],               // 94
            ['Jim',          'Collins'],            // 95
            ['Simon',        'Sinek'],              // 96
            ['Cal',          'Newport'],            // 97
            ['Daniel',       'Kahneman'],           // 98
            ['Nelson',       'Mandela'],            // 99
            ['Anne',         'Frank'],              // 100
            ['Jon',          'Krakauer'],           // 101
            ['Tara',         'Westover'],           // 102
            ['Trevor',       'Noah'],               // 103
            ['Andre',        'Agassi'],             // 104
            ['Robert C.',    'Martin'],             // 105
            ['Frederick',    'Brooks'],             // 106
            ['Martin',       'Fowler'],             // 107
            ['James',        'Clear'],              // 108
            ['Walter',       'Isaacson'],           // 109
            ['André',        'Gide'],               // 110
            ['André',        'Malraux'],            // 111
            ['Marguerite',   'Duras'],              // 112
            ['Samuel',       'Beckett'],            // 113
            ['Eugène',       'Ionesco'],            // 114
            ['Georges',      'Perec'],              // 115
            ['Patrick',      'Modiano'],            // 116
            ['Michel',       'Houellebecq'],        // 117
            ['James',        'Joyce'],              // 118
            ['Virginia',     'Woolf'],              // 119
            ['D.H.',         'Lawrence'],           // 120
            ['E.M.',         'Forster'],            // 121
            ['Graham',       'Greene'],             // 122
            ['William',      'Faulkner'],           // 123
            ['Philip',       'Roth'],               // 124
            ['Arthur C.',    'Clarke'],             // 125
            ['Brandon',      'Sanderson'],          // 126
            ['H.P.',         'Lovecraft'],          // 127
            ['Edgar Allan',  'Poe'],                // 128
            ['Jorge Luis',   'Borges'],             // 129
            ['Chinua',       'Achebe'],             // 130
            ['J.M.',         'Coetzee'],            // 131
            ['Lewis',        'Carroll'],            // 132
            ['Roald',        'Dahl'],               // 133
            ['René',         'Descartes'],          // 134
            ['Jean-Jacques', 'Rousseau'],           // 135
            ['Friedrich',    'Nietzsche'],          // 136
            ['Aldous',       'Huxley'],             // 137
            ['Umberto',      'Eco'],                // 138
            ['Thomas',       'Mann'],               // 139
            ['Herman',       'Hesse'],              // 140
            ['Johann Wolfgang von', 'Goethe'],     // 141
            ['Dante',        'Alighieri'],          // 142
            ['Miguel de',    'Cervantes'],          // 143
            ['Homère',       ''],                   // 144
            ['Malcolm',      'Gladwell'],           // 145
            ['Nassim Nicholas', 'Taleb'],           // 146
            ['Yuval Noah',   'Harari'],             // 147
            ['Thomas',       'Hardy'],              // 148
            ['Evelyn',       'Waugh'],              // 149
            ['Stéphane',     'Mallarmé'],           // 150
            ['Arthur',       'Rimbaud'],            // 151
            ['Paul',         'Verlaine'],           // 152
            ['Charles',      'Baudelaire'],         // 153
            ['François',     'Rabelais'],           // 154
            ['Montesquieu',  ''],                   // 155
            ['Victor',       'Hugo'],               // 156 (Hugo 2e entrée pour Hernani)
            ['Iain M.',      'Banks'],              // 157
            ['Peter F.',     'Hamilton'],           // 158
            ['Shirley',      'Jackson'],              // 159
            ['Ivan',          'Tourgueniev'],          // 160
            ['Anton',         'Tchékhov'],             // 161
            ['Yukio',         'Mishima'],              // 162
            ['Yasunari',      'Kawabata'],             // 163
            ['Natsume',       'Soseki'],               // 164
            ['Mario Vargas',  'Llosa'],                // 165
            ['Carlos',        'Fuentes'],              // 166
            ['Jorge',         'Amado'],                // 167
            ['Isabel',        'Allende'],              // 168
            ['Octavio',       'Paz'],                  // 169
            ['François',      'Mauriac'],              // 170
            ['Georges',       'Bernanos'],             // 171
            ['Louis-Ferdinand','Céline'],              // 172
            ['Colette',       ''],                     // 173
            ['Georges',       'Simenon'],              // 174
            ['Marguerite',    'Yourcenar'],            // 175
            ['J.M.G.',        'Le Clézio'],            // 176
            ['Don',           'DeLillo'],              // 177
            ['Paul',          'Auster'],               // 178
            ['Robin',         'Hobb'],                 // 179
            ['Fred',          'Vargas'],               // 180
            ['Jean-Christophe','Grangé'],              // 181
            ['James',         'Ellroy'],               // 182
            ['Vladimir',      'Nabokov'],              // 183
            ['Milan',         'Kundera'],              // 184
            ['José',          'Saramago'],             // 185
            ['Romain',        'Gary'],                 // 186
            ['Patrick',       'Süskind'],              // 187
            ['Ken',           'Follett'],              // 188
            ['John',          'Le Carré'],             // 189
            ['Ian',           'Fleming'],              // 190
            ['Michael',       'Crichton'],             // 191
            ['Fernando',      'Pessoa'],               // 192
            ['Walt',          'Whitman'],              // 193
            ['T.S.',          'Eliot'],                // 194
            ['Naguib',        'Mahfouz'],              // 195
            ['Günter',        'Grass'],                // 196
            ['Primo',         'Levi'],                 // 197
            ['Italo',         'Calvino'],              // 198
            ['Stefan',        'Zweig'],                // 199
            ['Hannah',        'Arendt'],               // 200
            ['Blaise',        'Pascal'],               // 201
            ['Amin',          'Maalouf'],              // 202
            ['Tahar Ben',     'Jelloun'],              // 203
            ['Paul',          'Éluard'],               // 204
            ['Jacques',       'Prévert'],              // 205
            ['Guillaume',     'Apollinaire'],          // 206
            ['Alphonse',      'Daudet'],               // 207
            ['Anatole',       'France'],               // 208
            ['Jean',          'Giono'],                // 209
            ['Patrick',       'Chamoiseau'],           // 210
            ['Donna',         'Tartt'],                // 211
            ['Harlan',        'Coben'],                // 212
            ['Marc',          'Levy'],                 // 213
            ['Guillaume',     'Musso'],                // 214
            ['Gaston',        'Leroux'],               // 215
            ['Maurice',       'Leblanc'],              // 216
            ['Dino',          'Buzzati'],              // 217
            ['Arthur',        'Schnitzler'],           // 218
            ['Friedrich',     'Schiller'],             // 219
            ['Virgile',       ''],                     // 220
            ['Sophocle',      ''],                     // 221
            ['Dario',         'Fo'],                   // 222
            ['Jules',         'Michelet'],             // 223
            ['Fernand',       'Braudel'],              // 224
        ] as [$fn, $ln]) {
            $author = new Author();
            $author->setFirstName($fn)->setLastName($ln);
            $manager->persist($author);
            $authors[] = $author;
        }

        // ── Utilisateurs ─────────────────────────────────────────────────────
        $admin = new User();
        $admin->setEmail('admin@biblioconnect.fr')->setFirstName('Admin')->setLastName('Principal')->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin123!'));
        $manager->persist($admin);

        $librarian = new User();
        $librarian->setEmail('librarian@biblioconnect.fr')->setFirstName('Marie')->setLastName('Dupont')->setRoles(['ROLE_LIBRARIAN']);
        $librarian->setPassword($this->hasher->hashPassword($librarian, 'Libra123!'));
        $manager->persist($librarian);

        $user = new User();
        $user->setEmail('user@biblioconnect.fr')->setFirstName('Jean')->setLastName('Martin');
        $user->setPassword($this->hasher->hashPassword($user, 'User1234!'));
        $manager->persist($user);

        // ── Livres ────────────────────────────────────────────────────────────
        // [titre, description, langue, stock, [catégories], [idx auteurs], date publication, cover_id Open Library]
        $booksData = [
            // ── Classiques français ──────────────────────────────────────────
            ['Les Misérables',                     'Le chef-d\'œuvre de Victor Hugo sur la misère, la justice et la rédemption dans la France du XIXe siècle.',      'fr', 4, ['Roman'],                            [0],       '1862-01-01', 11012366],
            ['Notre-Dame de Paris',                'L\'histoire du bossu Quasimodo, d\'Esmeralda et de l\'archidiacre Frollo autour de la cathédrale gothique.',     'fr', 2, ['Roman'],                            [0],       '1831-03-16', 2626880],
            ['L\'Homme qui rit',                   'Victor Hugo décrit la misère et la difformité au service d\'une satire de l\'aristocratie anglaise.',            'fr', 2, ['Roman'],                            [0],       '1869-01-01', 977794],
            ['Quatrevingt-treize',                 'Le dernier roman de Victor Hugo, sur la Révolution française et la guerre de Vendée.',                            'fr', 2, ['Roman', 'Histoire'],                 [0],       '1874-01-01', 8245196],
            ['Madame Bovary',                      'Portrait d\'une femme romantique étouffée par la médiocrité de la vie provinciale au XIXe siècle.',              'fr', 3, ['Roman'],                            [6],       '1857-04-01', 8379199],
            ['L\'Éducation sentimentale',          'L\'histoire de la passion de Frédéric Moreau pour Mme Arnoux, tableau de la vie parisienne sous Louis-Philippe.','fr', 2, ['Roman'],                            [6],       '1869-01-01', 11411335],
            ['Salammbô',                           'Roman historique de Flaubert sur Carthage et la guerre des Mercenaires au IIIe siècle av. J.-C.',               'fr', 1, ['Roman', 'Histoire'],                 [6],       '1862-01-01', 3078356],
            ['À la recherche du temps perdu',      'Œuvre monumentale de Proust explorant la mémoire, le temps et la société française de la Belle Époque.',        'fr', 1, ['Roman', 'Philosophie'],             [7],       '1913-11-14', 12332709],
            ['Du côté de chez Swann',              'Premier volume de la Recherche : l\'enfance de Marcel à Combray et l\'amour de Swann pour Odette.',             'fr', 2, ['Roman', 'Philosophie'],             [7],       '1913-11-14', 11967344],
            ['Germinal',                           'Zola plonge dans le monde des mineurs du Nord et dépeint une grève dans des conditions dramatiques.',            'fr', 3, ['Roman', 'Histoire'],                [8],       '1885-03-02', 9607011],
            ['L\'Assommoir',                       'Zola décrit la déchéance d\'une blanchisseuse, Gervaise, dans le Paris ouvrier du Second Empire.',              'fr', 3, ['Roman'],                            [8],       '1877-01-01', 13504150],
            ['Nana',                               'La fille de Gervaise, Nana, devient une grande courtisane parisienne dans la haute société du Second Empire.',   'fr', 2, ['Roman'],                            [8],       '1880-01-01', 8237804],
            ['Au Bonheur des Dames',               'Zola raconte l\'essor d\'un grand magasin parisien et ses effets sur le commerce traditionnel.',                'fr', 2, ['Roman'],                            [8],       '1883-01-01', 13144784],
            ['La Bête humaine',                    'Roman noir de Zola mêlant le meurtre et la passion dans le milieu ferroviaire normand.',                        'fr', 2, ['Roman', 'Policier'],                 [8],       '1890-01-01', 8243599],
            ['Le Rouge et le Noir',                'Julien Sorel, jeune ambitieux, gravit les échelons de la société française de la Restauration.',                'fr', 2, ['Roman'],                            [9],       '1830-11-01', 3082938],
            ['La Chartreuse de Parme',             'Les aventures amoureuses et politiques de Fabrice del Dongo dans l\'Italie napoléonienne et post-napoléonienne.','fr', 2, ['Roman', 'Histoire'],                [9],       '1839-01-01', 11662748],
            ['Le Petit Prince',                    'Conte philosophique et poétique sur l\'amitié, l\'amour et le sens de la vie.',                                 'fr', 5, ['Roman', 'Philosophie', 'Jeunesse'], [10],      '1943-04-06', 12390127],
            ['Vingt mille lieues sous les mers',   'Jules Verne embarque ses lecteurs dans un voyage extraordinaire à bord du Nautilus du capitaine Nemo.',         'fr', 3, ['Roman', 'Science-fiction'],         [11],      '1870-01-01', 6573517],
            ['Le Tour du monde en 80 jours',       'Phileas Fogg parie qu\'il peut faire le tour du monde en quatre-vingts jours.',                                 'fr', 3, ['Roman'],                            [11],      '1872-01-01', 6976035],
            ['Voyage au centre de la Terre',       'Le professeur Lidenbrock et son neveu descendent dans les profondeurs de la Terre par un volcan islandais.',    'fr', 3, ['Roman', 'Science-fiction'],         [11],      '1864-01-01', 5890987],
            ['De la Terre à la Lune',              'Une société d\'artilleurs lance un projectile habité vers la Lune depuis la Floride.',                          'fr', 3, ['Roman', 'Science-fiction'],         [11],      '1865-01-01', 5943556],
            ['Michel Strogoff',                    'Un courrier du Tsar traverse la Russie en guerre pour rejoindre Irkoutsk malgré une invasion tartare.',         'fr', 2, ['Roman', 'Histoire'],                [11],      '1876-01-01', 10393209],
            ['Les Trois Mousquetaires',            'Les aventures d\'Athos, Porthos, Aramis et d\'Artagnan au service du roi de France.',                           'fr', 4, ['Roman', 'Histoire'],                [12],      '1844-01-01', 11929973],
            ['Le Comte de Monte-Cristo',           'Edmond Dantès, injustement emprisonné, s\'évade et se venge de ceux qui l\'ont trahi.',                         'fr', 3, ['Roman', 'Histoire'],                [12],      '1844-01-01', 14566393],
            ['Vingt ans après',                    'Suite des Trois Mousquetaires, les héros se retrouvent séparés par les guerres de la Fronde.',                  'fr', 2, ['Roman', 'Histoire'],                [12],      '1845-01-01', 14564526],
            ['La Reine Margot',                    'La nuit de la Saint-Barthélemy et les intrigues de la cour des Valois sous Charles IX.',                        'fr', 2, ['Roman', 'Histoire'],                [12],      '1845-01-01', 14557277],
            ['Bel-Ami',                            'L\'ascension sociale cynique de Georges Duroy dans le Paris de la presse et de la politique.',                  'fr', 2, ['Roman'],                            [13],      '1885-05-01', 3097415],
            ['Une vie',                            'La vie monotone et douloureuse de Jeanne, une aristocrate normande déçue par l\'amour et la maternité.',        'fr', 2, ['Roman'],                            [13],      '1883-01-01', 6866195],
            ['Pierre et Jean',                     'Deux frères que tout oppose lorsque l\'un d\'eux hérite d\'une fortune mystérieuse.',                           'fr', 2, ['Roman'],                            [13],      '1888-01-01', 10523857],
            ['Candide',                            'Conte philosophique de Voltaire, satire mordante de l\'optimisme et des guerres du XVIIIe siècle.',             'fr', 3, ['Philosophie', 'Roman'],             [14],      '1759-01-01', 12736044],
            ['L\'Étranger',                        'Roman d\'Albert Camus sur l\'absurde : Meursault commet un meurtre sans raison apparente.',                     'fr', 4, ['Roman', 'Philosophie'],             [2],       '1942-05-19', 3126372],
            ['La Peste',                           'Camus décrit la lutte des habitants d\'Oran face à une épidémie, métaphore de l\'Occupation.',                 'fr', 3, ['Roman', 'Philosophie'],             [2],       '1947-06-10', 13151272],
            ['La Chute',                           'Un avocat parisien déchu confesse ses fautes à un inconnu dans un bar d\'Amsterdam.',                           'fr', 2, ['Roman', 'Philosophie'],             [2],       '1956-01-01', 8296477],
            ['Le Mythe de Sisyphe',                'Camus explore le concept d\'absurde et la question du suicide philosophique.',                                   'fr', 2, ['Philosophie'],                      [2],       '1942-01-01', 1014395],
            ['Le Père Goriot',                     'Balzac décrit la dévotion d\'un père ruiné pour ses filles ingrates dans une pension parisienne.',             'fr', 3, ['Roman'],                            [30],      '1835-01-01', 15002799],
            ['Illusions perdues',                  'L\'ascension et la chute de Lucien de Rubempré dans le Paris littéraire et politique du XIXe siècle.',          'fr', 2, ['Roman'],                            [30],      '1837-01-01', 5752320],
            ['Eugénie Grandet',                    'L\'histoire d\'une jeune femme opprimée par son père avare dans une ville de province.',                        'fr', 2, ['Roman'],                            [30],      '1833-01-01', 8237025],
            ['La Cousine Bette',                   'Bette Fischer tisse sa vengeance contre sa famille dans le Paris des années 1830.',                             'fr', 2, ['Roman'],                            [30],      '1846-01-01', 8243268],
            ['La Nausée',                          'Roquentin, seul dans une ville de province, éprouve une répulsion viscérale envers le monde.',                 'fr', 2, ['Roman', 'Philosophie'],             [79],      '1938-01-01', 3126412],
            ['Huis clos',                          'Trois personnages morts se retrouvent enfermés ensemble dans l\'enfer : « L\'enfer, c\'est les autres ».',      'fr', 2, ['Théâtre', 'Philosophie'],           [79],      '1944-01-01', 7227937],
            ['L\'Être et le Néant',                'L\'œuvre maîtresse de Sartre fondant l\'existentialisme : la conscience, la liberté et la mauvaise foi.',       'fr', 1, ['Philosophie'],                      [79],      '1943-01-01', 10847649],
            ['Le Deuxième Sexe',                   'Simone de Beauvoir analyse la condition féminine et pose les bases du féminisme moderne.',                       'fr', 2, ['Philosophie'],                      [78],      '1949-01-01', 78169],
            ['Les Mandarins',                      'Prix Goncourt 1954 : le monde des intellectuels de gauche dans l\'après-guerre parisien.',                      'fr', 2, ['Roman'],                            [78],      '1954-01-01', 1013685],
            ['Le Misanthrope',                     'Alceste, homme d\'une sincérité absolue, se heurte à l\'hypocrisie de la société.',                             'fr', 2, ['Théâtre'],                          [31],      '1666-01-01', 3082519],
            ['Tartuffe',                           'Un faux dévot s\'introduit dans une famille bourgeoise et s\'y installe par la flatterie.',                     'fr', 3, ['Théâtre'],                          [31],      '1664-01-01', 2146228],
            ['L\'Avare',                           'Harpagon, avare notoire, s\'oppose aux amours de ses enfants et cherche lui-même à se remarier.',               'fr', 2, ['Théâtre'],                          [31],      '1668-01-01', 3097402],
            ['Phèdre',                             'Phèdre, éprise de son beau-fils Hippolyte, déclenche une tragédie irréversible.',                              'fr', 2, ['Théâtre'],                          [32],      '1677-01-01', 11527671],
            ['Le Cid',                             'Rodrigue doit choisir entre son amour pour Chimène et l\'honneur de son père.',                                'fr', 2, ['Théâtre'],                          [33],      '1637-01-01', 8236984],
            // ── Science-fiction ──────────────────────────────────────────────
            ['1984',                               'George Orwell décrit un régime totalitaire omniscient où la pensée libre est un crime.',                        'en', 3, ['Roman', 'Science-fiction'],         [1],       '1949-06-08', 9267242],
            ['La Ferme des animaux',               'Les animaux d\'une ferme se révoltent contre les humains mais reproduisent bientôt leur oppression.',           'en', 3, ['Roman', 'Science-fiction'],         [1],       '1945-08-17', 11261770],
            ['Le Meilleur des mondes',             'Aldous Huxley imagine une société futuriste où le bonheur artificiel remplace la liberté.',                     'fr', 2, ['Science-fiction', 'Philosophie'],   [],        '1932-01-01', 14887879],
            ['Dune',                               'Épopée galactique de Frank Herbert sur Arrakis, planète désertique seule source de l\'épice la plus précieuse.','en', 2, ['Science-fiction'],                  [3],       '1965-08-01', 11481354],
            ['Le Messie de Dune',                  'Paul Atréides est maintenant l\'Empereur de l\'univers, mais la prophétie prend une tournure sombre.',          'en', 2, ['Science-fiction'],                  [3],       '1969-01-01', 2421405],
            ['Les Enfants de Dune',                'Les jumeaux de Paul doivent sauver Arrakis d\'une nouvelle oppression.',                                        'en', 1, ['Science-fiction'],                  [3],       '1976-01-01', 6976407],
            ['Fondation',                          'Isaac Asimov décrit la chute d\'un empire galactique et les efforts d\'Hari Seldon pour en réduire les conséquences.','en', 3, ['Science-fiction'],           [4],       '1951-05-01', 14612610],
            ['Fondation et Empire',                'La Fondation affronte l\'Empire agonisant et la menace imprévisible du Mulet.',                                 'en', 2, ['Science-fiction'],                  [4],       '1952-01-01', 9300695],
            ['Seconde Fondation',                  'La Fondation cherche à retrouver la mystérieuse Seconde Fondation qui tire les ficelles en secret.',            'en', 2, ['Science-fiction'],                  [4],       '1953-01-01', 9261324],
            ['Les Cavernes d\'acier',              'Le détective Elijah Baley enquête sur un meurtre avec R. Daneel, un robot humanoïde, dans une Terre surpeuplée.','en', 2, ['Science-fiction', 'Policier'],    [4],       '1954-01-01', 13790511],
            ['Fahrenheit 451',                     'Dans un futur où les livres sont brûlés, un pompier commence à douter du système.',                             'en', 2, ['Science-fiction', 'Roman'],         [27],      '1953-10-19', 8225482],
            ['Les Chroniques martiennes',          'Ray Bradbury décrit l\'exploration et la colonisation de Mars à travers des nouvelles poétiques et mélancoliques.','en', 2, ['Science-fiction'],              [27],      '1950-01-01', 9346537],
            ['Ubik',                               'Philip K. Dick explore les frontières entre réalité et illusion dans un futur perturbant.',                     'en', 2, ['Science-fiction'],                  [26],      '1969-01-01', 5018327],
            ['Le Jeu d\'Ender',                    'Ender Wiggin, un enfant prodige, est entraîné pour sauver l\'humanité contre une race extraterrestre.',         'en', 3, ['Science-fiction', 'Jeunesse'],     [49],      '1985-01-01', 12996033],
            ['Hypérion',                           'Dan Simmons compose un space opera inspiré de Keats, avec sept pèlerins se rendant vers le mystérieux Gritche.','en', 2, ['Science-fiction'],                 [50],      '1989-01-01', 380332],
            ['Neuromancien',                       'William Gibson pose les bases du cyberpunk avec Case, hacker engagé dans une mission dans le cyberespace.',     'en', 2, ['Science-fiction'],                  [51],      '1984-01-01', 283860],
            ['Le Guide du voyageur galactique',    'La Terre est démolie pour faire place à une rocade hyperspatiale. Arthur Dent part en auto-stop dans la galaxie.','en', 3, ['Science-fiction', 'Jeunesse'],   [52],      '1979-01-01', 12986869],
            ['En terre étrangère',                 'Valentine Michael Smith, élevé par des Martiens, arrive sur Terre et bouleverse la société humaine.',           'en', 1, ['Science-fiction'],                  [53],      '1961-01-01', 14630668],
            ['Solaris',                            'Un psychiatre explore une station orbitant autour d\'un océan vivant et pensant sur Solaris.',                  'en', 2, ['Science-fiction', 'Philosophie'],   [54],      '1961-01-01', 12313764],
            ['La Main gauche de la nuit',          'Un envoyé terrien explore une planète dont les habitants n\'ont pas de sexe fixe.',                             'en', 2, ['Science-fiction'],                  [55],      '1969-01-01', 10618463],
            ['La Servante écarlate',               'Dans la République de Gilead, les femmes fertiles sont réduites au rang de reproductrices.',                   'en', 3, ['Science-fiction', 'Roman'],         [56],      '1985-01-01', 2943057],
            ['Des fleurs pour Algernon',           'Charlie Gordon, handicapé mental, devient un génie grâce à une opération puis observe sa propre déchéance.',   'en', 2, ['Science-fiction', 'Roman'],         [57],      '1966-01-01', 12947700],
            // ── Fantasy ──────────────────────────────────────────────────────
            ['Le Seigneur des Anneaux',            'La quête épique de Frodon pour détruire l\'Anneau Unique et vaincre Sauron en Terre du Milieu.',              'fr', 2, ['Fantasy', 'Roman'],                  [5],       '1954-07-29', 14625765],
            ['Le Hobbit',                          'Les aventures de Bilbo Sacquet parti à la reconquête du royaume nain avec Gandalf et treize nains.',           'fr', 3, ['Fantasy', 'Jeunesse'],             [5],       '1937-09-21', 14627509],
            ['Le Silmarillion',                    'La mythologie de la Terre du Milieu : la création du monde et les grandes guerres des Premier et Second Âges.', 'fr', 2, ['Fantasy'],                         [5],       '1977-01-01', 370441],
            ['Harry Potter et la pierre philosophale','Le jeune Harry Potter découvre qu\'il est un sorcier et entre à l\'école de Poudlard.',                     'fr', 5, ['Fantasy', 'Jeunesse'],             [15],      '1997-06-26', 15155833],
            ['Harry Potter et la chambre des secrets','La deuxième année de Harry à Poudlard : une série d\'attaques mystérieuses terrorise l\'école.',           'fr', 4, ['Fantasy', 'Jeunesse'],             [15],      '1998-07-02', 8228798],
            ['Harry Potter et le prisonnier d\'Azkaban','Harry apprend qu\'un dangereux prisonnier s\'est évadé d\'Azkaban et semble le rechercher.',             'fr', 4, ['Fantasy', 'Jeunesse'],             [15],      '1999-07-08', 0],
            ['Harry Potter et la coupe de feu',   'Harry est mystérieusement inscrit dans un dangereux tournoi de magie entre sorciers.',                          'fr', 4, ['Fantasy', 'Jeunesse'],             [15],      '2000-07-08', 0],
            ['Harry Potter et l\'Ordre du Phénix','Harry et ses amis affrontent Voldemort et son armée de Mangemorts dans un ministère de la magie en crise.',    'fr', 3, ['Fantasy', 'Jeunesse'],             [15],      '2003-06-21', 0],
            ['Harry Potter et le Prince de Sang-Mêlé','Dumbledore révèle à Harry les secrets du passé de Voldemort.',                                              'fr', 3, ['Fantasy', 'Jeunesse'],             [15],      '2005-07-16', 10716273],
            ['Harry Potter et les Reliques de la Mort','La bataille finale entre Harry et Voldemort pour le sort du monde des sorciers.',                          'fr', 3, ['Fantasy', 'Jeunesse'],             [15],      '2007-07-21', 15158660],
            ['Le Trône de fer',                    'George R.R. Martin dépeint des luttes de pouvoir impitoyables dans un monde médiéval fantastique.',            'en', 2, ['Fantasy', 'Roman'],                [58],      '1996-08-01', 9269962],
            ['Le Clash des rois',                  'Cinq rois se disputent le trône de Westeros tandis que des menaces surnaturelles grandissent au nord.',        'en', 2, ['Fantasy', 'Roman'],                [58],      '1998-11-16', 8231751],
            ['Le Nom du vent',                     'Kvothe, le légendaire magicien, raconte l\'histoire de sa vie à un chroniqueur dans une auberge isolée.',       'en', 2, ['Fantasy', 'Roman'],               [59],      '2007-03-27', 11480483],
            ['L\'Œil du monde',                    'Premier tome de La Roue du Temps : un groupe de jeunes villageois fuit les créatures des Ténèbres.',            'en', 2, ['Fantasy'],                         [60],      '1990-01-15', 980232],
            ['American Gods',                      'Shadow Moon est libéré de prison et se retrouve mêlé à une guerre entre anciennes et nouvelles divinités.',     'en', 2, ['Fantasy', 'Roman'],               [61],      '2001-06-19', 8494659],
            ['Bons présages',                      'Un ange et un démon s\'allient pour empêcher l\'Apocalypse car ils ont tous deux trop aimé la Terre.',          'en', 2, ['Fantasy', 'Jeunesse'],            [61, 62],  '1990-05-01', 10482258],
            ['La Couleur de la magie',             'Le premier roman du Disque-Monde : Rincevent, le pire magicien du monde, guide un touriste naïf.',              'en', 2, ['Fantasy', 'Jeunesse'],            [62],      '1983-01-01', 14647238],
            // ── Policier / Thriller ──────────────────────────────────────────
            ['Sherlock Holmes : Étude en rouge',   'La première enquête du célèbre détective Sherlock Holmes et du Dr Watson.',                                     'fr', 3, ['Policier'],                         [24],      '1887-11-01', 13405534],
            ['Dix petits nègres',                  'Dix inconnus réunis sur une île isolée meurent les uns après les autres selon une comptine.',                  'fr', 4, ['Policier'],                         [25],      '1939-11-06', 8410117],
            ['Le Meurtre de Roger Ackroyd',        'Hercule Poirot enquête sur le meurtre d\'un notable anglais dans un retournement majeur du roman policier.',   'fr', 3, ['Policier'],                         [25],      '1926-06-13', 13151356],
            ['Le Grand Sommeil',                   'Le détective Philip Marlowe s\'enfonce dans les bas-fonds de Los Angeles pour retrouver une femme disparue.',  'en', 2, ['Policier'],                         [63],      '1939-01-01', 7268475],
            ['Le Faucon maltais',                  'Sam Spade enquête sur le meurtre de son associé et la mystérieuse affaire de la statuette du faucon.',         'en', 2, ['Policier'],                         [64],      '1930-01-01', 998587],
            ['De sang-froid',                      'Truman Capote retrace minutieusement le massacre de la famille Clutter dans le Kansas en 1959.',               'en', 2, ['Policier', 'Biographie'],           [65],      '1966-01-01', 228066],
            ['Millénium 1 : Les hommes qui n\'aimaient pas les femmes','Lisbeth Salander et Mikael Blomkvist enquêtent sur la disparition d\'une jeune fille.',    'fr', 3, ['Policier', 'Thriller'],             [66],      '2005-01-01', 9274740],
            ['Les Apparences',                     'Nick Dunne est le premier suspect quand sa femme Amy disparaît le jour de leur anniversaire de mariage.',      'en', 3, ['Thriller', 'Policier'],             [67],      '2012-06-05', 8368314],
            ['Da Vinci Code',                      'Un cryptologue et une cryptographe enquêtent sur un meurtre au Louvre impliquant les secrets du christianisme.','en', 4, ['Thriller', 'Policier'],            [68],      '2003-04-01', 9255229],
            ['Anges et Démons',                    'Robert Langdon découvre un complot des Illuminati contre le Vatican et la communauté scientifique.',            'en', 3, ['Thriller', 'Policier'],             [68],      '2000-05-01', 11408459],
            // ── Littérature anglaise classique ───────────────────────────────
            ['Orgueil et Préjugés',                'Elizabeth Bennet et Mr. Darcy, entre préjugés de classe et orgueil blessé, apprennent à se voir tels qu\'ils sont.','en', 4, ['Roman'],                       [34],      '1813-01-28', 853465],
            ['Raison et Sentiments',               'Deux sœurs cherchent leur bonheur entre raison et romantisme dans l\'Angleterre du XVIIIe siècle.',            'en', 3, ['Roman'],                            [34],      '1811-10-30', 9278292],
            ['Emma',                               'Emma Woodhouse, jeune femme riche et espiègle, s\'amuse à arranger des mariages autour d\'elle.',              'en', 3, ['Roman'],                            [34],      '1815-12-23', 9278312],
            ['Jane Eyre',                          'L\'orpheline Jane Eyre devient gouvernante dans le sombre manoir de Thornfield et s\'éprend de Mr. Rochester.','en', 3, ['Roman'],                            [35],      '1847-10-16', 10770819],
            ['Les Hauts de Hurlevent',             'La passion destructrice entre Heathcliff et Catherine sur les landes du Yorkshire.',                           'en', 3, ['Roman'],                            [36],      '1847-12-01', 381410],
            ['Middlemarch',                        'George Eliot peint la vie de province anglaise et les aspirations contrariées de Dorothea Brooke.',            'en', 2, ['Roman'],                            [37],      '1871-12-01', 2367694],
            ['Frankenstein',                       'Victor Frankenstein crée une créature qu\'il rejette et qui se venge sur sa famille et ses amis.',             'en', 3, ['Roman', 'Science-fiction'],         [46],      '1818-01-01', 12356249],
            ['Le Portrait de Dorian Gray',         'Dorian Gray vend son âme pour que son portrait vieillisse à sa place tandis qu\'il reste éternellement jeune.','en', 3, ['Roman', 'Philosophie'],             [45],      '1890-01-01', 6816209],
            ['L\'Étrange Cas du Dr Jekyll et Mr Hyde','Un médecin respectable libère sa part sombre à travers une potion mystérieuse.',                            'en', 3, ['Roman', 'Horreur'],                  [48],      '1886-01-01', 295773],
            // ── Littérature américaine ───────────────────────────────────────
            ['Les Aventures de Tom Sawyer',        'Les espiègleries de Tom Sawyer dans la ville de Saint Petersburg sur les rives du Mississippi.',               'en', 3, ['Roman', 'Jeunesse'],                [38],      '1876-01-01', 11389564],
            ['Les Aventures de Huckleberry Finn',  'Huck Finn s\'échappe de chez lui et descend le Mississippi sur un radeau avec Jim, un esclave fugitif.',      'en', 3, ['Roman'],                            [38],      '1884-12-01', 6379845],
            ['Moby Dick',                          'Le capitaine Achab mène son équipage dans une quête obsessionnelle et mortelle contre la baleine blanche.',    'en', 2, ['Roman'],                            [39],      '1851-10-18', 10410387],
            ['La Lettre écarlate',                 'Hester Prynne est condamnée à porter une lettre A rouge pour son adultère dans la Nouvelle-Angleterre puritaine.','en', 2, ['Roman'],                        [40],      '1850-03-16', 6603956],
            ['Des souris et des hommes',           'L\'amitié entre George et Lennie, deux ouvriers agricoles itinérants qui rêvent d\'un bout de terre bien à eux.','en', 3, ['Roman'],                         [28],      '1937-01-01', 14319003],
            ['À l\'est d\'Éden',                   'Steinbeck retrace l\'histoire de deux familles dans la vallée de Salinas, une épopée américaine inspirée de la Bible.','en', 2, ['Roman'],                   [28],      '1952-01-01', 11386937],
            ['Gatsby le Magnifique',               'Jay Gatsby, milliardaire mystérieux, organise des fêtes fastueuses pour reconquérir son ancien amour.',        'en', 3, ['Roman'],                            [41],      '1925-04-10', 8432032],
            ['Tendre est la nuit',                 'Dick Diver, psychiatre brillant, se noie dans le monde des expatriés américains sur la Côte d\'Azur.',         'en', 2, ['Roman'],                            [41],      '1934-04-12', 6984433],
            ['Pour qui sonne le glas',             'Un Américain rejoint les brigades internationales pendant la guerre civile espagnole et vit trois jours intenses.','en', 2, ['Roman', 'Histoire'],            [22],      '1940-10-21', 3872000],
            ['L\'Adieu aux armes',                 'Un lieutenant américain tombe amoureux d\'une infirmière pendant la Première Guerre mondiale.',                'en', 2, ['Roman', 'Histoire'],                [22],      '1929-09-27', 7226599],
            ['Le Soleil se lève aussi',            'Un groupe d\'expatriés américains et anglais vivent une saison à Pampelune lors des fêtes de San Fermín.',     'en', 2, ['Roman'],                            [22],      '1926-10-22', 78741],
            ['Ne tirez pas sur l\'oiseau moqueur', 'Atticus Finch défend un homme noir accusé de viol dans l\'Alabama des années 1930.',                           'en', 3, ['Roman'],                            [42],      '1960-07-11', 12784310],
            ['Va poster une sentinelle',           'Scout Finch retourne à Maycomb et découvre une nouvelle facette de son père Atticus.',                          'en', 2, ['Roman'],                            [42],      '2015-07-14', 7383195],
            ['Catch-22',                           'Yossarian, bombardier américain pendant la Seconde Guerre mondiale, lutte contre une armée absurde.',           'en', 2, ['Roman'],                            [81],      '1961-11-10', 12448429],
            ['Vol au-dessus d\'un nid de coucou', 'Randle McMurphy simule la folie pour éviter la prison et se retrouve dans un hôpital psychiatrique oppressif.', 'en', 2, ['Roman'],                            [82],      '1962-02-01', 9272688],
            ['L\'Attrape-cœurs',                  'Holden Caulfield, adolescent rebelle, erre dans New York après son renvoi de pensionnat.',                       'en', 3, ['Roman'],                            [43],      '1951-07-16', 10737898],
            ['Sa Majesté des Mouches',             'Des enfants échoués sur une île déserte créent une société qui dégénère rapidement vers la violence.',          'en', 3, ['Roman', 'Philosophie'],             [44],      '1954-09-17', 8684447],
            ['Abattoir 5',                         'Billy Pilgrim, « déstabilisé dans le temps », revit sans cesse le bombardement de Dresde en 1945.',            'en', 2, ['Roman', 'Science-fiction'],         [80],      '1969-03-31', 12727001],
            // ── Horreur ──────────────────────────────────────────────────────
            ['Ça',                                 'Dans la ville de Derry, un groupe d\'enfants affronte une entité terrifiante qui se manifeste sous forme de clown.','fr', 2, ['Horreur', 'Roman'],              [16],      '1986-09-15', 8569284],
            ['Shining',                            'Jack Torrance, gardien d\'un hôtel isolé, sombre dans la folie sous l\'influence des forces surnaturelles du lieu.','fr', 2, ['Horreur', 'Roman'],             [16],      '1977-01-28', 12376585],
            ['Carrie',                             'Carrie White, adolescente persécutée et dotée de pouvoirs télékinésiques, se venge lors du bal de promo.',     'fr', 2, ['Horreur'],                          [16],      '1974-04-05', 9256043],
            ['Misery',                             'L\'écrivain Paul Sheldon est séquestré par sa plus grande fan après un accident de voiture.',                   'fr', 2, ['Horreur', 'Thriller'],              [16],      '1987-06-08', 8259296],
            ['La Ligne verte',                     'Les gardiens du couloir de la mort découvrent des dons surnaturels chez un condamné, John Coffey.',             'fr', 2, ['Horreur', 'Roman'],                 [16],      '1996-01-01', 9334567],
            ['Simetierre',                         'Un cimetière indien où les morts reviennent à la vie, mais différents, change à jamais la famille Creed.',      'fr', 2, ['Horreur'],                          [16],      '1983-11-14', 12015500],
            // ── Littérature mondiale ─────────────────────────────────────────
            ['Cent ans de solitude',               'García Márquez raconte l\'histoire de la famille Buendía sur sept générations à Macondo.',                     'es', 2, ['Roman'],                             [18],      '1967-05-30', 12627383],
            ['L\'Amour aux temps du choléra',      'Florentino Ariza attend cinquante ans pour retrouver Fermina Daza, l\'amour de sa vie.',                       'es', 2, ['Roman'],                             [18],      '1985-01-01', 10096404],
            ['Crime et Châtiment',                 'Dostoïevski explore la psychologie d\'un étudiant qui commet un meurtre et lutte avec sa conscience.',          'fr', 3, ['Roman', 'Philosophie'],             [19],      '1866-01-01', 9411873],
            ['Les Frères Karamazov',               'Le dernier et plus grand roman de Dostoïevski : le meurtre d\'un père et le procès de ses trois fils.',        'fr', 2, ['Roman', 'Philosophie'],             [19],      '1880-01-01', 8272336],
            ['L\'Idiot',                           'Le prince Mychkine, homme d\'une bonté absolue, est broyé par la société russe corrompue.',                    'fr', 2, ['Roman'],                             [19],      '1868-01-01', 11226648],
            ['Guerre et Paix',                     'Tolstoï peint la société russe pendant les guerres napoléoniennes à travers plusieurs familles.',              'fr', 1, ['Roman', 'Histoire'],                 [20],      '1869-01-01', 12621906],
            ['Anna Karénine',                      'Anna Karénine brise les conventions sociales en quittant son mari pour l\'officier Vronski.',                  'fr', 2, ['Roman'],                             [20],      '1878-01-01', 2560652],
            ['La Mort d\'Ivan Ilitch',             'Un juge de haut rang confronté à la mort remet en question toute sa vie.',                                      'fr', 2, ['Roman', 'Philosophie'],             [20],      '1886-01-01', 419697],
            ['La Métamorphose',                    'Kafka raconte le réveil de Gregor Samsa transformé en insecte et l\'effondrement de sa famille.',              'fr', 4, ['Roman', 'Philosophie'],             [21],      '1915-10-15', 12820198],
            ['Le Vieil Homme et la Mer',           'Un vieux pêcheur cubain combat seul contre un gigantesque marlin en haute mer.',                               'fr', 3, ['Roman'],                             [22],      '1952-09-01', 14344172],
            ['Kafka sur le rivage',                'Haruki Murakami entrelace deux récits parallèles dans un roman onirique et symbolique.',                        'fr', 2, ['Roman'],                             [17],      '2002-09-12', 4982600],
            ['La Forêt norvégienne',               'Toru Watanabe se souvient de sa jeunesse à Tokyo dans les années 60 et de ses amours tragiques.',              'fr', 2, ['Roman'],                             [17],      '1987-01-01', 2237620],
            ['Chronique du vent et de la pluie',  'Murakami décrit la saga de deux familles liées sur plusieurs générations au Japon.',                             'fr', 2, ['Roman'],                             [17],      '1994-01-01', 630454],
            ['Hamlet',                             'Le prince du Danemark cherche à venger le meurtre de son père, tourmenté par le doute et la trahison.',        'fr', 3, ['Théâtre', 'Roman'],                  [23],      '1603-01-01', 8281954],
            ['Les Raisins de la colère',           'Steinbeck raconte l\'exode d\'une famille de fermiers chassée vers la Californie pendant la Grande Dépression.','fr', 2, ['Roman', 'Histoire'],               [28],      '1939-04-14', 12715902],
            ['Oliver Twist',                       'Charles Dickens dépeint la vie des orphelins et des bas-fonds de Londres au XIXe siècle.',                     'fr', 3, ['Roman'],                             [29],      '1838-01-01', 9289570],
            ['Un Conte de deux villes',            'Dickens dépeint les souffrances de Paris et de Londres pendant la Révolution française.',                       'fr', 2, ['Roman', 'Histoire'],                [29],      '1859-01-01', 8324308],
            ['Grandes Espérances',                 'Pip, orphelin issu du peuple, aspire à devenir un gentleman grâce à un bienfaiteur mystérieux.',               'fr', 2, ['Roman'],                             [29],      '1861-01-01', 13322313],
            ['David Copperfield',                  'Roman semi-autobiographique de Dickens : l\'ascension de David depuis une enfance misérable.',                 'fr', 2, ['Roman'],                             [29],      '1850-01-01', 1048892],
            ['Un Chant de Noël',                   'Le vieil avare Ebenezer Scrooge est visité par trois esprits qui lui montrent le sens de Noël.',               'fr', 2, ['Roman', 'Jeunesse'],                [29],      '1843-12-19', 12875748],
            ['Le Maître et Marguerite',            'Boulgakov entrelace la visite du diable à Moscou soviétique et la passion de Ponce Pilate pour Jésus.',        'fr', 2, ['Roman'],                             [75],      '1967-01-01', 12947486],
            ['Le Docteur Jivago',                  'L\'histoire d\'amour de Youri Jivago et Lara sur fond de révolution bolchevique et de guerre civile russe.',   'fr', 2, ['Roman', 'Histoire'],                [76],      '1957-01-01', 1045432],
            ['Les Enfants de minuit',              'Saleem Sinai, né au moment de l\'indépendance de l\'Inde, partage un lien mystérieux avec tous les enfants nés à cette heure.','en', 2, ['Roman'],            [73],      '1981-01-01', 8346713],
            ['Les Vestiges du jour',               'Un majordome anglais réévalue sa vie en réfléchissant à sa dévotion aveugle à un maître collaborateur.',       'en', 2, ['Roman'],                             [74],      '1989-01-01', 95742],
            ['Auprès de moi toujours',             'Dans un futur proche, des êtres humains clonés pour fournir des organes découvrent progressivement leur destin.','en', 2, ['Roman', 'Science-fiction'],        [74],      '2005-03-03', 1047334],
            ['L\'Alchimiste',                      'Paulo Coelho raconte le voyage initiatique de Santiago, un berger andalou à la recherche de son destin.',      'fr', 4, ['Roman', 'Philosophie'],             [77],      '1988-01-01', 7414780],
            // ── Informatique ─────────────────────────────────────────────────
            ['Clean Code',                         'Robert C. Martin explique les principes pour écrire un code lisible, maintenable et professionnel.',           'en', 2, ['Informatique'],                      [105],     '2008-08-11', 8065615],
            ['The Pragmatic Programmer',           'David Thomas et Andrew Hunt livrent les meilleures pratiques du développeur moderne.',                          'en', 2, ['Informatique'],                      [],        '1999-10-30', 10143650],
            ['Design Patterns',                    'Les patterns de conception incontournables du Gang of Four pour architecturer les logiciels.',                  'en', 1, ['Informatique'],                      [],        '1994-10-31', 8508505],
            ['Le Mythe du mois-homme',             'Brooks démontre pourquoi ajouter des personnes à un projet en retard le retarde davantage.',                   'en', 1, ['Informatique'],                      [106],     '1975-01-01', 6915361],
            ['Code Complete',                      'McConnell rassemble les meilleures pratiques de la construction logicielle dans un guide de référence.',       'en', 1, ['Informatique'],                      [],        '1993-01-01', 461500],
            ['Refactoring',                        'Martin Fowler explique comment améliorer la conception du code existant sans changer son comportement.',        'en', 1, ['Informatique'],                      [107],     '1999-01-01', 7087623],
            ['Structure and Interpretation of Computer Programs','Abelson et Sussman enseignent les fondements de la programmation avec Lisp.',                    'en', 1, ['Informatique'],                      [],        '1984-01-01', 149338],
            ['Introduction aux algorithmes',       'Cormen et al. proposent la référence mondiale sur les algorithmes et structures de données.',                  'en', 1, ['Informatique'],                      [],        '1990-07-12', 2341462],
            ['L\'Art de la programmation',         'Donald Knuth livre l\'encyclopédie de la programmation informatique : algorithmes et structures de données.',   'en', 1, ['Informatique'],                      [],        '1968-01-01', 136600],
            ['Cracking the Coding Interview',      'Gayle Laakmann McDowell prépare aux entretiens des grandes entreprises tech avec 189 questions et solutions.', 'en', 2, ['Informatique'],                      [],        '2011-01-01', 7276811],
            ['You Don\'t Know JS',                 'Kyle Simpson plonge en profondeur dans les mécanismes subtils de JavaScript.',                                  'en', 2, ['Informatique'],                      [],        '2015-01-01', 8117575],
            // ── Philosophie ──────────────────────────────────────────────────
            ['Ainsi parlait Zarathoustra',         'Nietzsche expose ses grandes idées : le surhomme, la volonté de puissance et l\'éternel retour.',              'fr', 2, ['Philosophie'],                       [],        '1883-01-01', 1017466],
            ['L\'Art de la guerre',                'Sun Tzu livre les stratégies militaires et diplomatiques qui ont traversé 2500 ans d\'histoire.',               'fr', 3, ['Philosophie', 'Histoire'],          [],        '0500-01-01', 4849549],
            ['Méditations',                        'Marc Aurèle, empereur romain, livre ses réflexions sur la vertu, la mort et la conduite de la vie.',           'fr', 3, ['Philosophie'],                       [],        '0175-01-01', 13202688],
            ['La République',                      'Platon élabore sa vision de la cité juste, de la justice, et du philosophe-roi.',                              'fr', 2, ['Philosophie'],                       [84],      '0380-01-01', 14418448],
            ['L\'Éthique',                         'Spinoza démontre more geometrico que Dieu, la nature et l\'homme ne font qu\'un.',                            'fr', 1, ['Philosophie'],                       [85],      '1677-01-01', 8244655],
            ['Critique de la raison pure',         'Kant interroge les conditions et les limites de la connaissance humaine.',                                      'fr', 1, ['Philosophie'],                       [86],      '1781-01-01', 1994279],
            ['La Phénoménologie de l\'Esprit',     'Hegel décrit le voyage de la conscience vers la connaissance absolue à travers l\'Histoire.',                  'fr', 1, ['Philosophie'],                       [87],      '1807-01-01', 8245249],
            ['Le Capital',                         'Marx analyse le mode de production capitaliste et pose les bases de la théorie de la valeur-travail.',          'fr', 1, ['Philosophie', 'Économie'],           [88],      '1867-01-01', 10995820],
            ['Être et Temps',                      'Heidegger interroge le sens de l\'être à travers une analyse du Dasein (l\'être-là) dans le monde.',           'fr', 1, ['Philosophie'],                       [89],      '1927-01-01', 2208564],
            // ── Développement personnel / Business ───────────────────────────
            ['Atomic Habits',                      'James Clear explique comment de petites habitudes peuvent transformer radicalement une vie.',                   'en', 4, ['Développement personnel'],           [108],     '2018-10-16', 12539702],
            ['Comment se faire des amis',          'Dale Carnegie enseigne les principes fondamentaux pour influencer positivement les autres.',                    'en', 4, ['Développement personnel'],           [90],      '1936-01-01', 13314878],
            ['Réfléchissez et devenez riche',      'Napoleon Hill analyse les habitudes des hommes les plus riches du monde pour en tirer des leçons.',            'en', 3, ['Développement personnel'],           [91],      '1937-03-01', 14542536],
            ['Les 7 habitudes des gens efficaces', 'Stephen Covey propose un programme complet de développement personnel fondé sur des principes éternels.',       'en', 3, ['Développement personnel'],           [92],      '1989-08-15', 10079937],
            ['Zero to One',                        'Peter Thiel explique comment créer des entreprises qui construisent l\'avenir plutôt que de copier le passé.', 'en', 2, ['Économie', 'Développement personnel'],[93],     '2014-09-16', 9002334],
            ['Le Modèle Lean Startup',             'Eric Ries explique comment les startups peuvent réussir grâce à l\'apprentissage validé et à l\'itération.',  'en', 2, ['Économie'],                          [94],      '2011-09-13', 7104760],
            ['De la performance à l\'excellence', 'Jim Collins identifie les facteurs qui permettent à certaines entreprises d\'être vraiment excellentes.',       'en', 2, ['Économie'],                          [95],      '2001-10-16', 53111],
            ['Commencer par pourquoi',             'Simon Sinek explique pourquoi les grands leaders inspirent l\'action en communiquant leur « pourquoi ».',       'en', 3, ['Développement personnel'],           [96],      '2009-10-29', 6395237],
            ['Travail profond',                    'Cal Newport argumente que la capacité de se concentrer sans distraction est la superpuissance du XXIe siècle.','en', 3, ['Développement personnel'],            [97],      '2016-01-05', 7988607],
            ['Système 1 / Système 2',              'Daniel Kahneman explore les deux modes de pensée qui gouvernent nos décisions et nos biais cognitifs.',         'en', 3, ['Développement personnel', 'Philosophie'],[98],  '2011-10-25', 15174454],
            // ── Biographies ──────────────────────────────────────────────────
            ['Steve Jobs',                         'Walter Isaacson dresse le portrait complet du cofondateur d\'Apple, génie créatif et manager impitoyable.',    'fr', 2, ['Biographie'],                        [109],     '2011-10-24', 12374726],
            ['Moi, Malala',                        'L\'autobiographie de la plus jeune lauréate du Prix Nobel de la Paix, militante pour l\'éducation.',           'fr', 3, ['Biographie'],                        [],        '2013-10-08', 9358664],
            ['Un long chemin vers la liberté',     'Nelson Mandela retrace sa vie, de son enfance en Afrique du Sud à sa présidence après 27 ans d\'emprisonnement.','en', 2, ['Biographie', 'Histoire'],         [99],      '1994-10-01', 12702407],
            ['Journal d\'Anne Frank',              'Le journal intime d\'une jeune juive cachée à Amsterdam pendant l\'Occupation nazie.',                         'fr', 4, ['Biographie', 'Histoire'],             [100],     '1947-06-25', 13521319],
            ['Into the Wild',                      'Jon Krakauer retrace l\'aventure de Christopher McCandless parti seul survivre dans les forêts d\'Alaska.',    'en', 2, ['Biographie'],                        [101],     '1996-01-13', 1377482],
            ['Educated',                           'Tara Westover grandit sans école dans une famille survivaliste et accède malgré tout à Cambridge et Harvard.',  'en', 3, ['Biographie'],                        [102],     '2018-02-20', 8314077],
            ['Une enfance ordinaire',              'Trevor Noah raconte avec humour son enfance métissée sous l\'apartheid sud-africain.',                         'en', 3, ['Biographie', 'Histoire'],             [103],     '2016-11-15', 8294078],
            ['Open',                               'Andre Agassi livre une autobiographie surprenante : sa haine du tennis, ses addictions et ses victoires.',     'en', 2, ['Biographie'],                        [104],     '2009-11-09', 6714053],

            // ── Littérature française XX-XXIe ────────────────────────────────
            ['L\'Immoraliste',                     'Michel Marcelline cherche à se libérer des contraintes morales et sociales lors d\'un voyage en Afrique du Nord.',  'fr', 2, ['Roman'],                            [110],     '1902-01-01', 0],
            ['La Porte étroite',                   'L\'amour impossible d\'Alissa et Jérôme, sacrifié au nom d\'une vocation spirituelle intransigeante.',             'fr', 2, ['Roman'],                            [110],     '1909-01-01', 0],
            ['Les Caves du Vatican',               'Une escroquerie religieuse ébranle la foi de l\'aristocratie catholique dans ce roman picaresque de Gide.',        'fr', 2, ['Roman'],                            [110],     '1914-01-01', 0],
            ['La Condition humaine',               'Malraux raconte l\'insurrection communiste de Shanghai en 1927 et les figures qui la font vivre et mourir.',        'fr', 2, ['Roman', 'Histoire'],                 [111],     '1933-01-01', 0],
            ['L\'Espoir',                          'Malraux plonge dans la guerre civile espagnole côté républicain, entre exaltation et désillusion.',                'fr', 2, ['Roman', 'Histoire'],                 [111],     '1937-01-01', 0],
            ['L\'Amant',                           'Duras raconte sa liaison adolescente avec un Chinois riche dans l\'Indochine française des années 1930.',           'fr', 3, ['Roman'],                            [112],     '1984-01-01', 7891060],
            ['Hiroshima mon amour',                'Une actrice française et un architecte japonais vivent une histoire d\'amour hantée par les mémoires de guerre.',  'fr', 2, ['Roman'],                            [112],     '1960-01-01', 0],
            ['En attendant Godot',                 'Deux vagabonds attendent indéfiniment un certain Godot qui ne vient jamais, dans ce chef-d\'œuvre de l\'absurde.', 'fr', 3, ['Théâtre', 'Philosophie'],           [113],     '1952-01-01', 8293217],
            ['La Cantatrice chauve',               'Une pièce anti-pièce d\'Ionesco qui parodie les conventions du théâtre et du langage bourgeois.',                  'fr', 2, ['Théâtre'],                          [114],     '1950-01-01', 0],
            ['Rhinocéros',                         'Les habitants d\'une ville se transforment progressivement en rhinocéros : métaphore du totalitarisme.',            'fr', 2, ['Théâtre'],                          [114],     '1959-01-01', 0],
            ['La Vie mode d\'emploi',              'Perec décrit les 99 appartements d\'un immeuble parisien, entremêlant des centaines d\'histoires et d\'objets.',   'fr', 1, ['Roman'],                            [115],     '1978-01-01', 0],
            ['Les Choses',                         'Un couple de jeunes Parisiens rêve d\'une vie confortable mais se heurte à leur propre vacuité.',                  'fr', 2, ['Roman'],                            [115],     '1965-01-01', 0],
            ['Dora Bruder',                        'Modiano reconstitue la vie d\'une adolescente juive disparue à Paris pendant l\'Occupation.',                      'fr', 2, ['Roman', 'Biographie'],               [116],     '1997-01-01', 0],
            ['Les Particules élémentaires',        'Houellebecq dresse un portrait acide de deux demi-frères représentant les impasses du monde contemporain.',        'fr', 2, ['Roman'],                            [117],     '1998-01-01', 0],
            ['La Carte et le territoire',          'Un artiste contemporain devient célèbre en photographiant des cartes Michelin, Prix Goncourt 2010.',               'fr', 2, ['Roman'],                            [117],     '2010-01-01', 0],
            ['Gargantua',                          'Rabelais narre les aventures du géant Gargantua, père de Pantagruel, dans un récit débordant d\'humour et d\'érudition.', 'fr', 1, ['Roman'],                    [154],     '1534-01-01', 0],
            ['De l\'esprit des lois',              'Montesquieu analyse les différents types de gouvernements et théorise la séparation des pouvoirs.',                 'fr', 1, ['Philosophie'],                      [155],     '1748-01-01', 0],
            ['Hernani',                            'Hugo déchaîne la bataille romantique avec ce drame en vers qui oppose l\'honneur à l\'amour.',                     'fr', 2, ['Théâtre', 'Histoire'],               [0],       '1830-03-25', 0],
            ['Les Fleurs du Mal',                  'Baudelaire explore le spleen, la beauté du mal et la quête d\'idéal dans ce recueil majeur de la poésie française.','fr', 2, ['Poésie'],                         [153],     '1857-06-25', 11527765],
            ['Poèmes saturniens',                  'Le premier recueil de Verlaine, empreint de mélancolie, de musicalité et d\'évocations paysagères.',               'fr', 1, ['Poésie'],                          [152],     '1866-01-01', 0],
            ['Une Saison en enfer',                'Rimbaud rompt avec la poésie traditionnelle dans ce texte autobiographique en prose d\'une intensité brûlante.',   'fr', 1, ['Poésie'],                          [151],     '1873-01-01', 0],

            // ── Littérature anglaise XX-XXIe ─────────────────────────────────
            ['Ulysse',                             'Joyce suit Leopold Bloom et Stephen Dedalus à travers une seule journée à Dublin dans ce roman fleuve révolutionnaire.','fr', 1, ['Roman'],                       [118],     '1922-02-02', 10817538],
            ['Les Gens de Dublin',                 'Quinze nouvelles qui dressent un portrait de la vie dublinoise, marquée par la paralysie morale et sociale.',       'fr', 2, ['Roman'],                            [118],     '1914-06-15', 0],
            ['Mrs Dalloway',                       'Woolf suit une journée dans la vie de Clarissa Dalloway préparant une soirée, entremêlée aux souvenirs d\'un ancien amour.','fr', 2, ['Roman'],                   [119],     '1925-05-14', 5706212],
            ['Les Vagues',                         'Six voix se succèdent pour raconter leurs vies de l\'enfance à la vieillesse dans ce roman expérimental de Woolf.','fr', 1, ['Roman'],                           [119],     '1931-10-08', 0],
            ['Orlando',                            'Orlando traverse quatre siècles d\'histoire anglaise en changeant de sexe, roman fantaisiste et féministe de Woolf.','fr', 2, ['Roman'],                          [119],     '1928-10-11', 0],
            ['L\'Amant de lady Chatterley',        'Lawrence raconte la passion d\'une aristocrate pour son garde-chasse, scandale à sa parution pour son érotisme.',   'fr', 2, ['Roman'],                            [120],     '1928-01-01', 0],
            ['Fils et Amants',                     'Lawrence explore le lien étouffant entre une mère et ses fils dans le milieu des mineurs du Nottinghamshire.',      'fr', 1, ['Roman'],                            [120],     '1913-01-01', 0],
            ['Route des Indes',                    'Forster dépeint les tensions entre colonisateurs britanniques et Indiens à travers l\'affaire Aziz.',               'fr', 2, ['Roman', 'Histoire'],               [121],     '1924-01-01', 0],
            ['Chambre avec vue',                   'Lucy Honeychurch en Italie découvre la liberté et l\'amour en s\'opposant aux conventions de l\'Angleterre edwardienne.','fr', 2, ['Roman'],                      [121],     '1908-01-01', 0],
            ['L\'Américain tranquille',            'Un journaliste britannique à Saïgon observe les manœuvres d\'un idéaliste américain dans la guerre d\'Indochine.', 'fr', 2, ['Roman', 'Histoire'],               [122],     '1955-01-01', 0],
            ['Le Troisième Homme',                 'Greene plonge dans le Vienne d\'après-guerre pour une intrigue noire autour d\'un trafiquant de pénicilline.',      'fr', 2, ['Policier'],                         [122],     '1950-01-01', 0],
            ['Le Bruit et la Fureur',              'Faulkner raconte la décadence des Compson à travers quatre narrateurs dont un idiot, en rupture totale avec le roman classique.','fr', 1, ['Roman'],              [123],     '1929-10-07', 0],
            ['Tandis que j\'agonise',              'Faulkner décrit le voyage d\'une famille du Mississippi pour enterrer la mère selon son dernier vœu.',              'fr', 1, ['Roman'],                            [123],     '1930-01-01', 0],
            ['Pastorale américaine',               'Roth raconte la trajectoire d\'un homme ordinaire broyé par la violence des années 1960 à travers sa fille gauchiste.','fr', 2, ['Roman'],                        [124],     '1997-04-15', 0],
            ['La Tache',                           'Un professeur accusé de racisme voit sa vie détruite lors d\'un scandale qui révèle ses secrets les plus profonds.','fr', 2, ['Roman'],                           [124],     '2000-05-02', 0],

            // ── Science-fiction (suite) ──────────────────────────────────────
            ['2001 : L\'Odyssée de l\'espace',     'Des singes préhistoriques découvrent un monolithe mystérieux. Des millions d\'années plus tard, un voyage vers Jupiter.','fr', 3, ['Science-fiction'],            [125],     '1968-07-01', 8259014],
            ['Les Enfants d\'Icare',               'Clarke imagine un contact avec une civilisation extraterrestre bienveillante qui plonge l\'humanité dans la paix.',  'fr', 2, ['Science-fiction'],                [125],     '1953-01-01', 0],
            ['Rendez-vous avec Rama',              'Un vaisseau cylindrique de 50 km pénètre dans le système solaire : un équipage l\'explore avant qu\'il reparte.', 'fr', 2, ['Science-fiction'],                  [125],     '1973-01-01', 0],
            ['Le Joueur de mars',                  'Philip K. Dick décrit une colonie martienne où la réalité et l\'hallucination se confondent dans le quotidien.',     'fr', 2, ['Science-fiction'],                [26],      '1964-01-01', 0],
            ['Les androïdes rêvent-ils de moutons électriques ?', 'L\'œuvre qui a inspiré Blade Runner : un chasseur traque des androïdes dans un monde post-apocalyptique.','fr', 3, ['Science-fiction'],           [26],      '1968-01-01', 8099558],
            ['La Culture : L\'Usage des armes',    'Iain M. Banks explore les dilemmes moraux d\'un agent de la Culture, civilisation post-humaine ultra-avancée.',      'fr', 1, ['Science-fiction'],               [157],     '1990-01-01', 0],
            ['La Chute de la Maison Usher',        'Les nouvelles fantastiques et horrifiques d\'Edgar Allan Poe, maître du suspense et de l\'horreur gothique.',        'fr', 2, ['Horreur', 'Roman'],               [128],     '1839-01-01', 0],
            ['L\'Appel de Cthulhu',                'Les nouvelles de Lovecraft plongent dans un univers cosmique où des entités indicibles menacent la santé mentale.',  'fr', 2, ['Horreur', 'Fantasy'],             [127],     '1926-01-01', 0],
            ['Les Montagnes hallucinées',          'Une expédition en Antarctique découvre une cité préhistorique peuplée de créatures terrifiantes de l\'Ancien Monde.','fr', 1, ['Horreur', 'Science-fiction'],     [127],     '1936-01-01', 0],
            ['Dracula',                            'Bram Stoker crée le mythe du vampire avec le comte Dracula, traqué par Van Helsing dans l\'Angleterre victorienne.', 'fr', 3, ['Horreur', 'Roman'],               [47],      '1897-05-26', 0],
            ['Hantise',                            'Shirley Jackson décrit quatre personnages isolés dans un manoir hanté, chef-d\'œuvre de la littérature fantastique.','fr', 2, ['Horreur'],                        [159],     '1959-01-01', 0],

            // ── Fantasy (suite) ───────────────────────────────────────────────
            ['La Voie des rois',                   'Brandon Sanderson ouvre l\'épopée de Stormlight Archive dans un monde dévasté par des tempêtes et des anciens dieux.','fr', 2, ['Fantasy'],                      [126],     '2010-08-31', 0],
            ['Mistborn : L\'Empire Ultime',        'Sanderson imagine un monde où les cendres tombent du ciel et où la magie repose sur l\'ingestion de métaux.',         'fr', 2, ['Fantasy'],                      [126],     '2006-07-17', 0],
            ['Autrefois',                          'Terry Pratchett et le Disque-Monde : la Mort tombe amoureuse d\'une mortelle et prend congé de ses fonctions.',      'fr', 2, ['Fantasy'],                       [62],      '1987-01-01', 0],
            ['La Huitième Couleur',                'Premier tome du Disque-Monde : deux personnages hauts en couleur traversent ce monde porté par quatre éléphants.',   'fr', 2, ['Fantasy', 'Jeunesse'],          [62],      '1983-01-01', 0],

            // ── Littérature mondiale ──────────────────────────────────────────
            ['Fictions',                           'Borges explore le temps, l\'infini et les labyrinthes de la connaissance dans des nouvelles d\'une précision absolue.','fr', 2, ['Roman', 'Philosophie'],         [129],     '1944-01-01', 0],
            ['L\'Aleph',                           'Borges réunit ici ses nouvelles les plus emblématiques autour de l\'espace, du miroir et de l\'identité.',             'fr', 2, ['Roman', 'Philosophie'],         [129],     '1949-01-01', 0],
            ['Le Festin nu',                       'William Burroughs libère la prose de toute contrainte narrative dans ce roman culte de la Beat Generation.',           'fr', 1, ['Roman'],                        [],        '1959-01-01', 0],
            ['Tout s\'effondre',                   'Achebe raconte la vie d\'Okonkwo, homme puissant d\'un village igbo du Nigeria, confronté à la colonisation britannique.','fr', 3, ['Roman', 'Histoire'],          [130],     '1958-01-01', 0],
            ['Disgrâce',                           'Coetzee décrit la chute d\'un professeur sud-africain après un scandale, dans l\'Afrique du Sud post-apartheid.',     'fr', 2, ['Roman'],                        [131],     '1999-01-01', 0],
            ['Alice au pays des merveilles',       'Alice suit un lapin blanc dans un pays imaginaire et absurde où les règles et la logique sont renversées.',            'fr', 4, ['Roman', 'Jeunesse'],            [132],     '1865-11-26', 8310209],
            ['Charlie et la chocolaterie',         'Charlie Bucket gagne l\'un des cinq billets d\'or pour visiter la chocolaterie fantastique de Mr. Willy Wonka.',     'fr', 4, ['Roman', 'Jeunesse'],            [133],     '1964-01-01', 8310162],
            ['Matilda',                            'Matilda est une petite fille prodige maltraitée par ses parents qui développe des pouvoirs télékinésiques.',           'fr', 4, ['Roman', 'Jeunesse'],            [133],     '1988-10-01', 0],
            ['Le Nom de la Rose',                  'Eco mêle roman policier et médiéval : un moine franciscain enquête sur des morts mystérieuses dans une abbaye.',      'fr', 3, ['Roman', 'Policier', 'Histoire'],[138],     '1980-01-01', 7692873],
            ['Le Pendule de Foucault',             'Eco tisse une conspiration ésotérique mondiale entre éditeurs de Milan et les mystères des templiers.',               'fr', 2, ['Roman', 'Thriller'],             [138],     '1988-01-01', 0],
            ['La Montagne magique',                'Mann suit Hans Castorp dans un sanatorium suisse où se jouent, sur sept ans, les destins de l\'Europe d\'avant 1914.','fr', 1, ['Roman'],                        [139],     '1924-01-01', 2472745],
            ['Les Buddenbrook',                    'La saga de la famille Buddenbrook, négociants de Lübeck, qui décline sur quatre générations.',                        'fr', 1, ['Roman'],                        [139],     '1901-01-01', 0],
            ['Siddhartha',                         'Hesse retrace le cheminement spirituel d\'un jeune Indien contemporain de Bouddha en quête d\'éveil.',               'fr', 4, ['Roman', 'Philosophie'],          [140],     '1922-01-01', 7898207],
            ['Le Loup des steppes',                'Harry Haller, homme entre deux mondes, se perd dans les plaisirs et les hallucinations du Berlin des années 20.',     'fr', 2, ['Roman', 'Philosophie'],          [140],     '1927-01-01', 0],
            ['Narcisse et Goldmund',               'La tension entre la vie contemplative et la vie active à travers l\'amitié de deux hommes au Moyen Âge.',             'fr', 2, ['Roman', 'Philosophie'],          [140],     '1930-01-01', 0],
            ['Faust',                              'Goethe donne à la légende de Faust sa forme définitive : le savant vend son âme au diable pour connaître le bonheur.','fr', 2, ['Poésie', 'Théâtre'],           [141],     '1808-01-01', 11534040],
            ['Les Souffrances du jeune Werther',   'Un jeune artiste sensible meurt d\'amour pour une femme inaccessible dans ce roman épistolaire fondateur du romantisme.','fr', 2, ['Roman'],                     [141],     '1774-01-01', 0],
            ['La Divine Comédie',                  'Dante voyage à travers l\'Enfer, le Purgatoire et le Paradis guidé par Virgile puis par Béatrice.',                  'fr', 1, ['Poésie', 'Philosophie'],         [142],     '1321-01-01', 0],
            ['Don Quichotte',                      'Cervantes crée le premier roman moderne : un hidalgo qui se prend pour un chevalier errant combat des moulins à vent.','fr', 2, ['Roman'],                       [143],     '1605-01-01', 10782741],
            ['L\'Iliade',                          'Homère raconte les derniers jours de la guerre de Troie : la colère d\'Achille, la mort d\'Hector, la gloire et le deuil.','fr', 2, ['Roman', 'Histoire'],       [144],     '0800-01-01', 359867],
            ['L\'Odyssée',                         'Le retour d\'Ulysse à Ithaque après la guerre de Troie, semé d\'embûches divines et de monstres légendaires.',        'fr', 2, ['Roman', 'Histoire'],            [144],     '0800-01-01', 7222246],
            ['Tess d\'Uberville',                  'Hardy raconte la tragédie de Tess, jeune femme de la campagne anglaise broyée par la société victorienne.',           'fr', 2, ['Roman'],                        [148],     '1891-01-01', 0],
            ['Retour au pays natal',               'Aimé Césaire forge la négritude dans ce long poème-fleuve qui réclame l\'identité et la fierté des peuples noirs.',   'fr', 1, ['Poésie'],                      [],        '1939-01-01', 0],

            // ── Philosophie (suite) ───────────────────────────────────────────
            ['Discours de la méthode',             'Descartes pose le fondement du rationalisme moderne : « Je pense, donc je suis ».',                                   'fr', 2, ['Philosophie'],                   [134],     '1637-01-01', 0],
            ['Méditations métaphysiques',          'Descartes cherche des vérités indubitables en reconstruisant la connaissance par le doute méthodique.',               'fr', 2, ['Philosophie'],                   [134],     '1641-01-01', 0],
            ['Du Contrat social',                  'Rousseau théorise la souveraineté populaire et la volonté générale, texte fondateur de la démocratie moderne.',       'fr', 2, ['Philosophie'],                   [135],     '1762-01-01', 0],
            ['Les Confessions',                    'Rousseau livre une autobiographie d\'une sincérité inédite, explorant son enfance, ses fautes et ses aspirations.',   'fr', 2, ['Philosophie', 'Biographie'],     [135],     '1782-01-01', 0],
            ['Par-delà le bien et le mal',         'Nietzsche critique la morale traditionnelle et pose les bases de sa philosophie de la puissance et de la création.',  'fr', 2, ['Philosophie'],                   [136],     '1886-01-01', 0],
            ['La Généalogie de la morale',         'Nietzsche retrace l\'histoire des valeurs morales et distingue morale des maîtres et morale des esclaves.',           'fr', 1, ['Philosophie'],                   [136],     '1887-01-01', 0],

            // ── Informatique (suite) ─────────────────────────────────────────
            ['The Clean Coder',                    'Robert C. Martin détaille les comportements et pratiques d\'un développeur professionnel responsable.',               'en', 1, ['Informatique'],                   [105],     '2011-05-13', 0],
            ['Domain-Driven Design',               'Eric Evans explique comment concevoir des logiciels complexes en alignant le code sur le domaine métier.',            'en', 1, ['Informatique'],                   [],        '2003-08-30', 0],
            ['Test Driven Development',            'Kent Beck décrit la pratique du développement guidé par les tests pour produire un code plus sûr et maintenable.',   'en', 1, ['Informatique'],                   [],        '2002-11-18', 0],
            ['The Pragmatic Programmer (2nd ed.)', 'Edition mise à jour du classique de Thomas et Hunt sur les bonnes pratiques du développement logiciel moderne.',     'en', 1, ['Informatique'],                   [],        '2019-09-23', 0],
            ['Working Effectively with Legacy Code','Michael Feathers propose des techniques concrètes pour reprendre en main et tester du code sans tests existants.',  'en', 1, ['Informatique'],                   [],        '2004-09-22', 0],

            // ── Business / Développement personnel (suite) ───────────────────
            ['Blink',                              'Gladwell explore le pouvoir des décisions inconscientes et rapides : quand notre cerveau pense sans penser.',         'en', 3, ['Développement personnel'],        [145],     '2005-01-11', 2492756],
            ['The Tipping Point',                  'Gladwell analyse comment les idées, produits et comportements se propagent et franchissent le seuil de l\'épidémie.','en', 3, ['Développement personnel'],        [145],     '2000-03-01', 0],
            ['Outliers',                           'Gladwell déconstruit le mythe du mérite individuel pour montrer comment le contexte et la chance façonnent le succès.','en', 3, ['Développement personnel'],      [145],     '2008-11-18', 6439022],
            ['Le Cygne noir',                      'Taleb théorise les événements imprévisibles à impact extrême et la manière dont l\'humanité les rationalise après coup.','fr', 3, ['Développement personnel', 'Économie'],[146], '2007-04-17', 5635031],
            ['Antifragile',                        'Taleb explore les systèmes qui se renforcent sous l\'effet du désordre et de la volatilité.',                         'en', 2, ['Développement personnel', 'Économie'],[146], '2012-11-27', 0],
            ['Sapiens',                            'Harari retrace l\'histoire de l\'humanité depuis l\'homo sapiens jusqu\'à la révolution cognitive, agricole et scientifique.','fr', 5, ['Biographie', 'Histoire'],[147], '2011-01-01', 8554161],
            ['Homo Deus',                          'Harari projette les grandes tendances du futur : immortalité, bonheur algorithmique et la fin de l\'humanisme.',       'fr', 3, ['Philosophie', 'Histoire'],        [147],     '2015-09-01', 0],
            ['21 leçons pour le XXIe siècle',      'Harari analyse les défis contemporains : intelligence artificielle, fake news, terrorisme et désillusion démocratique.','fr', 3, ['Philosophie'],                  [147],     '2018-08-30', 0],
            ['La Semaine de 4 heures',             'Tim Ferriss propose un mode de vie alternatif basé sur l\'automatisation, l\'externalisation et la liberté géographique.','fr', 2, ['Développement personnel'],   [],        '2007-01-01', 0],
            ['Rich Dad Poor Dad',                  'Robert Kiyosaki compare les approches financières de deux pères et révèle pourquoi les riches ne travaillent pas pour l\'argent.','fr', 3, ['Développement personnel', 'Économie'],[], '1997-01-01', 0],

            // ── Biographies (suite) ───────────────────────────────────────────
            ['La Puissance du moment présent',     'Eckhart Tolle guide le lecteur vers une conscience libérée du mental et ancrée dans l\'instant présent.',            'fr', 3, ['Développement personnel', 'Philosophie'],[], '1997-01-01', 0],
            ['Elon Musk',                          'Ashlee Vance trace le portrait du fondateur de Tesla et SpaceX, entre génie visionnaire et management brutal.',       'fr', 3, ['Biographie'],                        [],        '2015-05-19', 0],
            ['Les Confessions d\'un avocat',       'Jacques Vergès raconte sa carrière et ses dossiers les plus controversés dans une autobiographie sans concession.',   'fr', 1, ['Biographie'],                        [],        '2003-01-01', 0],
            ['Le Journal d\'un fou',               'Gogol donne la parole à un fonctionnaire qui sombre dans la folie en croyant être le roi d\'Espagne.',               'fr', 2, ['Roman'],                            [],        '1835-01-01', 0],
            ['Guerre et guerre',                   'László Krasznahorkai décrit un archiviste qui découvre un manuscrit qu\'il décide de copier partout dans le monde.', 'fr', 1, ['Roman'],                            [],        '1999-01-01', 0],

            // ── Littérature russe et slave (nouveaux titres) ──────────────────
            ['Pères et fils',                      'Tourgueniev dresse un portrait du conflit entre pères conservateurs et fils nihilistes dans la Russie des années 1860.', 'fr', 2, ['Roman'],             [160],     '1862-01-01', 0],
            ['Premier amour',                      'Un adolescent tombe éperdument amoureux de Zinaïda, une jeune femme capricieuse qui réserve ses faveurs à un autre.', 'fr', 2, ['Roman'],                  [160],     '1860-01-01', 0],
            ['Mumu',                               'Un serf muet s\'attache à une petite chienne que la maîtresse lui ordonne de noyer dans un récit poignant.',          'fr', 2, ['Roman'],                  [160],     '1852-01-01', 0],
            ['La Cerisaie',                        'Une aristocrate russe rentre dans son domaine familial pour le vendre, incapable d\'accepter la fin d\'un monde.',    'fr', 2, ['Théâtre'],                [161],     '1904-01-01', 0],
            ['La Mouette',                         'Un jeune écrivain rêve de gloire dans un milieu artistique qui ne lui reconnaît aucun talent.',                        'fr', 2, ['Théâtre'],                [161],     '1896-01-01', 0],
            ['L\'Oncle Vania',                     'Un homme a consacré sa vie à financer les travaux d\'un beau-père médiocre qu\'il prend soudain en haine.',           'fr', 2, ['Théâtre'],                [161],     '1899-01-01', 0],
            ['Trois sœurs',                        'Trois sœurs rêvent de retourner à Moscou mais ne parviennent jamais à quitter leur ville de province.',               'fr', 2, ['Théâtre'],                [161],     '1901-01-01', 0],
            ['Les Démons',                         'Dostoïevski décrit la décomposition d\'une société provinciale sous l\'influence d\'un révolutionnaire nihiliste.',   'fr', 1, ['Roman', 'Philosophie'],   [19],      '1872-01-01', 0],
            ['Les Nuits blanches',                 'Un rêveur solitaire rencontre une jeune femme attendant un ancien amour pendant quatre nuits à Saint-Pétersbourg.',   'fr', 2, ['Roman'],                  [19],      '1848-01-01', 0],
            ['Résurrection',                       'Un aristocrate reconnaît sur le banc des accusés la paysanne qu\'il avait séduite et abandonnée jadis.',              'fr', 1, ['Roman', 'Philosophie'],   [20],      '1899-01-01', 0],
            ['Hadji Mourat',                       'Le chef de guerre tchétchène Hadji Mourat passe au service de l\'armée russe pour sauver sa famille.',                'fr', 2, ['Roman', 'Histoire'],      [20],      '1912-01-01', 0],
            ['Lolita',                             'Humbert Humbert, professeur européen, développe une obsession pour Dolores Haze, une adolescente américaine.',        'fr', 1, ['Roman'],                  [183],     '1955-01-01', 0],
            ['Feu pâle',                           'Nabokov construit un roman-puzzle autour du commentaire délirant d\'un poème de 999 vers.',                           'fr', 1, ['Roman'],                  [183],     '1962-01-01', 0],
            ['Le Château',                         'K., arpenteur, arrive dans un village dominé par un château inaccessible et ne parvient jamais à joindre l\'autorité.','fr', 2, ['Roman', 'Philosophie'], [21],      '1926-01-01', 0],
            ['Le Procès',                          'Josef K. est arrêté un matin sans qu\'on lui explique son crime ni la nature du tribunal qui le juge.',               'fr', 3, ['Roman', 'Philosophie'],   [21],      '1925-01-01', 0],
            ['Amerika',                            'Karl Rossmann, renvoyé en Amérique par ses parents, erre dans un pays absurde et labyrinthique.',                     'fr', 1, ['Roman'],                  [21],      '1927-01-01', 0],

            // ── Littérature japonaise ─────────────────────────────────────────
            ['Le Pavillon d\'or',                  'Un jeune moine bègue met le feu au pavillon d\'or de Kyoto, symbole de la beauté absolue qu\'il ne peut posséder.', 'fr', 2, ['Roman'],                   [162],     '1956-01-01', 0],
            ['Confession d\'un masque',            'Un jeune homme découvre lentement son homosexualité dans le Japon des années 40 à travers une série de masques.',    'fr', 1, ['Roman'],                   [162],     '1949-01-01', 0],
            ['Neige de printemps',                 'Kiyoaki et Satoko vivent une passion impossible dans le Japon Taishō : premier tome de La Mer de la Fertilité.',     'fr', 2, ['Roman'],                   [162],     '1969-01-01', 0],
            ['Pays de neige',                      'Un dandy tokyoïte se rend dans une station thermale de montagne pour retrouver une geisha au destin tragique.',       'fr', 2, ['Roman'],                   [163],     '1947-01-01', 0],
            ['Les Belles Endormies',               'Un vieil homme passe ses nuits auprès de jeunes femmes endormies dans une maison mystérieuse.',                       'fr', 1, ['Roman'],                   [163],     '1961-01-01', 0],
            ['Je suis un chat',                    'Un chat observe avec ironie les travers de la bourgeoisie japonaise de l\'ère Meiji.',                                 'fr', 2, ['Roman'],                   [164],     '1906-01-01', 0],
            ['Kokoro',                             'Un jeune étudiant se lie d\'amitié avec un homme solitaire dont il découvrira le lourd secret après sa mort.',        'fr', 2, ['Roman', 'Philosophie'],   [164],     '1914-01-01', 0],

            // ── Littérature latino-américaine ─────────────────────────────────
            ['La Ville et les Chiens',             'Dans une académie militaire péruvienne, des cadets règlent leurs comptes de façon brutale et secrète.',               'es', 2, ['Roman'],                  [165],     '1963-01-01', 0],
            ['La Fête au Bouc',                    'Vargas Llosa explore les dernières heures de la dictature de Trujillo en République dominicaine.',                    'es', 2, ['Roman', 'Histoire'],      [165],     '2000-01-01', 0],
            ['La Mort d\'Artemio Cruz',            'En mourant, un politicien mexicain fait le bilan de ses trahisons dans une structure narrative fragmentée.',           'es', 1, ['Roman'],                  [166],     '1962-01-01', 0],
            ['Capitaines des sables',              'Des enfants des rues de Bahia forment un gang de pickpockets dans le Brésil des années 30.',                          'pt', 2, ['Roman'],                  [167],     '1937-01-01', 0],
            ['La Maison aux esprits',              'Saga familiale chilienne sur quatre générations mêlant réalisme magique et histoire politique.',                       'es', 3, ['Roman', 'Histoire'],      [168],     '1982-01-01', 0],
            ['Eva Luna',                           'Eva Luna, fille du peuple à l\'imagination débordante, raconte sa vie à travers l\'Amérique latine.',                 'es', 2, ['Roman'],                  [168],     '1987-01-01', 0],
            ['Le Labyrinthe de la solitude',       'Octavio Paz analyse le caractère mexicain, ses masques, ses mythes et sa solitude fondamentale.',                     'es', 1, ['Philosophie', 'Histoire'], [169],    '1950-01-01', 0],

            // ── Littérature française XXe siècle (nouveaux auteurs) ──────────
            ['Thérèse Desqueyroux',                'Une femme des Landes a tenté d\'empoisonner son mari et attend son jugement dans une famille qui étouffe.',           'fr', 2, ['Roman'],                  [170],     '1927-01-01', 0],
            ['Le Nœud de vipères',                 'Un vieux notaire écrit à ses enfants sa haine accumulée et découvre finalement l\'amour avant de mourir.',            'fr', 2, ['Roman'],                  [170],     '1932-01-01', 0],
            ['Journal d\'un curé de campagne',     'Un jeune prêtre malade tient son journal et lutte contre la tiédeur et le péché dans sa paroisse.',                  'fr', 2, ['Roman'],                  [171],     '1936-01-01', 0],
            ['Sous le soleil de Satan',            'L\'abbé Donissan rencontre le diable sous les traits d\'un maquignon et lutte pour sauver les âmes.',                'fr', 1, ['Roman'],                  [171],     '1926-01-01', 0],
            ['Voyage au bout de la nuit',          'Bardamu traverse la guerre, la colonisation et la misère dans un roman nihiliste, féroce et novateur.',               'fr', 2, ['Roman'],                  [172],     '1932-01-01', 0],
            ['Mort à crédit',                      'Ferdinand revit son enfance misérable à Paris, son père autoritaire et ses premières désillusions.',                  'fr', 1, ['Roman'],                  [172],     '1936-01-01', 0],
            ['La Vagabonde',                       'Renée Néré, artiste de music-hall divorcée, refuse l\'amour d\'un riche prétendant pour garder sa liberté.',         'fr', 2, ['Roman'],                  [173],     '1910-01-01', 0],
            ['Gigi',                               'Une adolescente élevée pour devenir courtisane résiste aux conventions de la Belle Époque.',                          'fr', 2, ['Roman', 'Jeunesse'],      [173],     '1944-01-01', 0],
            ['Claudine à l\'école',                'Claudine, jeune fille espiègle, raconte sa dernière année au lycée d\'une petite ville de Bourgogne.',               'fr', 2, ['Roman'],                  [173],     '1900-01-01', 0],
            ['Maigret tend un piège',              'Le commissaire Maigret traque un tueur de femmes dans le quartier des Ternes à Paris.',                               'fr', 3, ['Policier'],               [174],     '1955-01-01', 0],
            ['Le Chien jaune',                     'Un mystérieux chien jaune rôde à Concarneau pendant qu\'une série de crimes frappe les notables de la ville.',        'fr', 2, ['Policier'],               [174],     '1931-01-01', 0],
            ['L\'Affaire Saint-Fiacre',            'Maigret retourne au village de son enfance pour élucider la mort de la comtesse de Saint-Fiacre.',                   'fr', 2, ['Policier'],               [174],     '1932-01-01', 0],
            ['Le Port des brumes',                 'Maigret enquête dans un port normand brumeux sur un meurtre aux ramifications insoupçonnées.',                        'fr', 2, ['Policier'],               [174],     '1932-01-01', 0],
            ['Mémoires d\'Hadrien',                'L\'empereur Hadrien rédige sa vie pour son successeur Marc Aurèle dans une méditation sur le pouvoir et la mort.',   'fr', 2, ['Roman', 'Histoire', 'Biographie'], [175], '1951-01-01', 0],
            ['L\'Œuvre au noir',                   'Zénon, alchimiste du XVIe siècle, erre à travers l\'Europe en quête de liberté de pensée et paie de sa vie.',       'fr', 1, ['Roman', 'Histoire', 'Philosophie'], [175], '1968-01-01', 0],
            ['Le Désert',                          'Lalla, une jeune fille du bidonville, rencontre le fantôme d\'un guerrier berbère dans le Maroc des années 70.',      'fr', 2, ['Roman'],                  [176],     '1980-01-01', 0],
            ['La Quarantaine',                     'Deux frères débarquent à l\'Île Maurice en 1891 et se retrouvent en quarantaine sur un îlot de misère.',              'fr', 1, ['Roman', 'Histoire'],      [176],     '1995-01-01', 0],
            ['La Promesse de l\'aube',             'Romain Gary retrace sa relation fusionnelle avec sa mère et son aspiration à devenir un grand écrivain français.',    'fr', 3, ['Roman', 'Biographie'],   [186],     '1960-01-01', 0],
            ['La Vie devant soi',                  'Momo, orphelin arabe à Belleville, est élevé par Madame Rosa, ancienne prostituée juive rescapée d\'Auschwitz.',      'fr', 3, ['Roman'],                  [186],     '1975-01-01', 0],
            ['Les Racines du ciel',                'Un homme défend les éléphants d\'Afrique dans un roman qui célèbre la liberté et l\'idéalisme. Prix Goncourt 1956.', 'fr', 2, ['Roman'],                  [186],     '1956-01-01', 0],
            ['Le Parfum',                          'Jean-Baptiste Grenouille, né sans odeur personnelle, développe un odorat hors du commun et cherche le parfum absolu.','de', 3, ['Roman', 'Thriller'],      [187],     '1985-01-01', 0],
            ['Les Lettres de mon moulin',          'Alphonse Daudet décrit la Provence et ses habitants à travers des contes charmants et nostalgiques.',                 'fr', 2, ['Roman', 'Jeunesse'],      [207],     '1869-01-01', 0],
            ['Tartarin de Tarascon',               'Le vaillant Tartarin, bravache méridional, part chasser les lions en Afrique dans une aventure burlesque.',           'fr', 2, ['Roman'],                  [207],     '1872-01-01', 0],
            ['L\'Île des pingouins',               'Satire de l\'histoire de France et de l\'humanité à travers les aventures d\'une île peuplée de pingouins baptisés.','fr', 1, ['Roman', 'Philosophie'],   [208],     '1908-01-01', 0],
            ['Regain',                             'Panturle, dernier habitant d\'un village provençal abandonné, le repeuple avec une femme errante.',                   'fr', 2, ['Roman'],                  [209],     '1930-01-01', 0],
            ['Colline',                            'Des paysans de Haute-Provence voient leur existence bouleversée par des présages et des forces mystérieuses.',         'fr', 2, ['Roman'],                  [209],     '1929-01-01', 0],
            ['Texaco',                             'Marie-Sophie Laborieux retrace l\'histoire de sa famille et du quartier Texaco à Fort-de-France en Martinique.',      'fr', 1, ['Roman', 'Histoire'],      [210],     '1992-01-01', 0],

            // ── Littérature internationale ────────────────────────────────────
            ['L\'Insoutenable légèreté de l\'être', 'À Prague en 1968, deux amants vivent des philosophies de vie opposées dans ce roman majeur de Kundera.',            'fr', 3, ['Roman', 'Philosophie'],   [184],     '1984-01-01', 0],
            ['Le Livre du rire et de l\'oubli',    'Kundera tisse des histoires entrelacées sur la mémoire, l\'exil et le régime communiste tchèque.',                    'fr', 2, ['Roman', 'Philosophie'],   [184],     '1979-01-01', 0],
            ['La Plaisanterie',                     'Un étudiant envoie une carte postale ironique sur le communisme et sa vie est entièrement détruite.',                 'fr', 2, ['Roman'],                  [184],     '1967-01-01', 0],
            ['L\'Aveuglement',                     'Toute une ville devient soudainement aveugle et sombre dans la barbarie ; seule une femme peut voir.',                 'pt', 2, ['Roman', 'Philosophie'],   [185],     '1995-01-01', 0],
            ['L\'Évangile selon Jésus-Christ',     'Saramago réécrit la vie de Jésus en faisant du diable un personnage ambigu et presque sympathique.',                  'pt', 1, ['Roman', 'Philosophie'],   [185],     '1991-01-01', 0],
            ['Les Piliers de la Terre',            'La construction d\'une cathédrale gothique au XIIe siècle en Angleterre, saga médiévale d\'amour et de pouvoir.',     'en', 3, ['Roman', 'Histoire'],      [188],     '1989-01-01', 0],
            ['Un Monde sans fin',                  'Suite des Piliers de la Terre : nouveaux personnages dans la même Kingsbridge au XIVe siècle.',                       'en', 2, ['Roman', 'Histoire'],      [188],     '2007-09-18', 0],
            ['L\'Espion qui venait du froid',      'Un agent secret britannique mène une opération de désinformation contre l\'Allemagne de l\'Est en pleine Guerre froide.','en', 2, ['Policier', 'Thriller'], [189],   '1963-01-01', 0],
            ['La Taupe',                           'Alec Leamas doit identifier une taupe soviétique au sein des services de renseignement britanniques.',                 'en', 2, ['Policier', 'Thriller'],   [189],     '1974-01-01', 0],
            ['Casino Royale',                      'James Bond affronte Le Chiffre, agent soviétique, lors d\'une partie de baccarat à haut risque.',                     'en', 3, ['Policier', 'Thriller'],   [190],     '1953-04-13', 0],
            ['Dr No',                              'James Bond enquête sur la disparition d\'agents britanniques en Jamaïque et affronte le mystérieux Dr No.',            'en', 2, ['Policier', 'Thriller'],   [190],     '1958-03-31', 0],
            ['Goldfinger',                         'James Bond traque et démasque Auric Goldfinger, l\'homme qui aime trop l\'or.',                                        'en', 2, ['Policier', 'Thriller'],   [190],     '1959-03-23', 0],
            ['Jurassic Park',                      'Des scientifiques recréent des dinosaures dans un parc d\'attractions sur une île isolée avec des résultats catastrophiques.','en', 3, ['Science-fiction', 'Thriller'], [191], '1990-11-20', 0],
            ['La Sphère',                          'Une équipe de scientifiques plonge au fond du Pacifique pour explorer un vaisseau spatial vieux de trois siècles.',    'en', 2, ['Science-fiction', 'Thriller'], [191], '1987-06-12', 0],
            ['Timeline',                           'Un groupe d\'historiens se retrouve transporté dans l\'Angleterre médiévale en pleine guerre de Cent Ans.',            'en', 2, ['Science-fiction', 'Thriller'], [191], '1999-11-02', 0],
            ['Le Livre de l\'intranquillité',      'Bernardo Soares note ses rêveries, méditations et désillusions dans un journal fragmentaire et poétique.',             'pt', 1, ['Philosophie', 'Poésie'],   [192],     '1982-01-01', 0],
            ['Feuilles d\'herbe',                  'Le recueil fondateur de la poésie américaine : Whitman chante la démocratie, la nature, le corps et l\'âme.',         'en', 1, ['Poésie'],                 [193],     '1855-01-01', 0],
            ['La Terre vaine',                     'Poème-fleuve en cinq parties sur la désolation spirituelle de l\'Europe après la Première Guerre mondiale.',          'en', 1, ['Poésie'],                 [194],     '1922-01-01', 0],
            ['Impasse des Deux Palais',            'Premier tome de la Trilogie du Caire : la famille Abd al-Jawad dans l\'Égypte du début du XXe siècle.',               'fr', 2, ['Roman', 'Histoire'],      [195],     '1956-01-01', 0],
            ['Le Palais du désir',                 'La famille Abd al-Jawad traverse la révolution égyptienne de 1919 dans le deuxième tome de la trilogie.',             'fr', 1, ['Roman', 'Histoire'],      [195],     '1957-01-01', 0],
            ['Le Tambour',                         'Oskar Matzerath refuse de grandir et observe de son tambour la montée du nazisme en Dantzig.',                         'de', 1, ['Roman', 'Histoire'],      [196],     '1959-01-01', 0],
            ['Si c\'est un homme',                 'Primo Levi raconte son arrestation et sa déportation à Auschwitz avec lucidité et sans haine.',                        'fr', 3, ['Biographie', 'Histoire'], [197],     '1947-01-01', 0],
            ['La Trêve',                           'Levi décrit le long voyage de retour d\'Auschwitz à Turin à travers une Europe totalement dévastée.',                  'fr', 2, ['Biographie', 'Histoire'], [197],     '1963-01-01', 0],
            ['Les Villes invisibles',              'Marco Polo décrit à Kublai Khan des villes imaginaires dans un dialogue contemplatif et poétique.',                    'fr', 2, ['Roman', 'Philosophie'],   [198],     '1972-01-01', 0],
            ['Si par une nuit d\'hiver un voyageur','Calvino met en scène le lecteur lui-même qui cherche à finir un roman dont on ne lui donne jamais la suite.',        'fr', 2, ['Roman'],                  [198],     '1979-01-01', 0],
            ['Le Baron perché',                    'Cosimo Piovasco di Rondò monte dans un arbre à douze ans et ne redescend plus jamais de sa vie.',                     'fr', 2, ['Roman', 'Philosophie'],   [198],     '1957-01-01', 0],
            ['Le Vicomte pourfendu',               'Un vicomte est coupé en deux par un boulet de canon ; ses deux moitiés bonne et mauvaise mènent des vies séparées.', 'fr', 2, ['Roman', 'Philosophie'],   [198],     '1952-01-01', 0],
            ['Le Monde d\'hier',                   'Les mémoires de Stefan Zweig sur l\'Europe d\'avant 1914, sa culture et sa destruction progressive.',                  'fr', 2, ['Biographie', 'Histoire'], [199],     '1942-01-01', 0],
            ['La Confusion des sentiments',        'Un étudiant tombe sous le charme de son professeur, découvrant des émotions troublantes et ambiguës.',                 'fr', 2, ['Roman'],                  [199],     '1927-01-01', 0],
            ['Marie Stuart',                       'Zweig dresse la biographie romancée de la reine d\'Écosse, de son règne à son exécution.',                            'fr', 2, ['Biographie', 'Histoire'], [199],     '1935-01-01', 0],
            ['Vingt-quatre heures de la vie d\'une femme','Un vieux monsieur raconte à une pension la passion d\'une aristocrate pour un joueur de casino.',             'fr', 2, ['Roman'],                  [199],     '1927-01-01', 0],
            ['Les Origines du totalitarisme',      'Arendt analyse les racines de l\'antisémitisme, de l\'impérialisme et du totalitarisme nazi et stalinien.',           'fr', 1, ['Philosophie', 'Histoire'], [200],    '1951-01-01', 0],
            ['La Condition de l\'homme moderne',   'Arendt distingue travail, œuvre et action pour penser l\'espace politique à l\'ère industrielle.',                    'fr', 1, ['Philosophie'],             [200],     '1958-01-01', 0],
            ['Pensées',                            'Pascal livre ses réflexions sur la condition humaine, la foi, le divertissement et les raisons du cœur.',              'fr', 2, ['Philosophie'],             [201],     '1670-01-01', 0],
            ['Léon l\'Africain',                   'Maalouf retrace la vie de Léon l\'Africain, géographe né à Grenade et voyageur du monde musulman de la Renaissance.', 'fr', 3, ['Roman', 'Histoire'],      [202],     '1986-01-01', 0],
            ['Les Croisades vues par les Arabes',  'Maalouf retrace les croisades du point de vue arabe, révélant un regard totalement différent sur l\'histoire.',       'fr', 2, ['Histoire'],               [202],     '1983-01-01', 0],
            ['Samarcande',                         'Maalouf entremêle la vie du poète Omar Khayyâm et l\'histoire du manuscrit des Rubaïyat.',                            'fr', 2, ['Roman', 'Histoire'],      [202],     '1988-01-01', 0],
            ['La Nuit sacrée',                     'Zahra découvre son identité de femme dans le Maroc traditionnel. Suite de L\'Enfant de sable, Prix Goncourt 1987.',   'fr', 2, ['Roman'],                  [203],     '1987-01-01', 0],
            ['L\'Enfant de sable',                 'Ahmed est élevé comme un garçon pour satisfaire le souhait d\'un père n\'ayant que des filles.',                      'fr', 2, ['Roman'],                  [203],     '1985-01-01', 0],
            ['Paroles',                            'Recueil de poèmes de Jacques Prévert, entre humour, tendresse, révolte et lyrisme populaire.',                         'fr', 2, ['Poésie'],                 [205],     '1946-01-01', 0],
            ['Alcools',                            'Apollinaire fait le bilan de ses amours et de sa modernité dans un recueil qui abolit la ponctuation.',                'fr', 1, ['Poésie'],                 [206],     '1913-01-01', 0],
            ['Calligrammes',                       'Apollinaire invente les poèmes-images et consigne ses expériences de la Première Guerre mondiale.',                    'fr', 1, ['Poésie'],                 [206],     '1918-01-01', 0],
            ['Capitale de la douleur',             'Éluard chante l\'amour et la douleur dans ce recueil surréaliste dédié à Gala.',                                      'fr', 1, ['Poésie'],                 [204],     '1926-01-01', 0],
            ['Le Désert des Tartares',             'Un officier passe toute sa vie dans une forteresse à attendre une attaque ennemie qui ne viendra peut-être jamais.',  'fr', 2, ['Roman'],                  [217],     '1940-01-01', 0],
            ['La Ronde',                           'Dix personnages forment une ronde amoureuse à Vienne fin XIXe : chacun est partenaire du suivant.',                   'fr', 1, ['Théâtre'],                [218],     '1897-01-01', 0],
            ['Les Brigands',                       'Karl Moor, noble idéaliste, devient chef de brigands pour se venger de son frère et d\'une société corrompue.',       'fr', 1, ['Théâtre'],                [219],     '1781-01-01', 0],
            ['Guillaume Tell',                     'Schiller chante la résistance héroïque de l\'archer suisse contre le tyran autrichien Gessler.',                      'fr', 1, ['Théâtre', 'Histoire'],    [219],     '1804-01-01', 0],
            ['L\'Énéide',                          'Virgile chante les errances du Troyen Énée à travers la Méditerranée et la fondation de Rome.',                       'fr', 1, ['Poésie', 'Histoire'],     [220],     '0029-01-01', 0],
            ['Œdipe roi',                          'Sophocle expose la tragédie du roi Œdipe qui découvre avoir tué son père et épousé sa mère.',                         'fr', 2, ['Théâtre', 'Philosophie'], [221],     '0429-01-01', 0],
            ['Antigone',                           'Antigone défie les lois de la cité pour enterrer son frère selon les rites divins et paie de sa vie.',                'fr', 3, ['Théâtre', 'Philosophie'], [221],     '0441-01-01', 0],
            ['Mort accidentelle d\'un anarchiste', 'Un fou s\'introduit dans une commission d\'enquête sur la mort mystérieuse d\'un anarchiste tombé d\'une fenêtre.',   'fr', 1, ['Théâtre'],                [222],     '1970-01-01', 0],
            ['Histoire de France',                 'Michelet retrace l\'histoire de France de ses origines à la Révolution avec un lyrisme épique et passionné.',         'fr', 1, ['Histoire'],               [223],     '1833-01-01', 0],
            ['Histoire de la Révolution française','Michelet retrace avec passion la Révolution française comme acte de naissance de la nation française.',               'fr', 1, ['Histoire'],               [223],     '1853-01-01', 0],
            ['La Méditerranée',                    'Braudel offre une histoire totale de la Méditerranée au temps de Philippe II, mêlant géographie, économie et société.','fr', 1, ['Histoire'],              [224],     '1949-01-01', 0],

            // ── Policier / Thriller français ──────────────────────────────────
            ['Pars vite et reviens tard',          'Le commissaire Adamsberg enquête sur des signes peints sur les portes de Paris et des cadavres atypiques.',           'fr', 2, ['Policier'],               [180],     '2001-01-01', 0],
            ['L\'Homme aux cercles bleus',         'Adamsberg enquête sur des cercles bleus tracés sur le trottoir parisien et les corps retrouvés à l\'intérieur.',      'fr', 2, ['Policier'],               [180],     '1991-01-01', 0],
            ['Un lieu incertain',                  'Une série de meurtres atypiques relie une morgue londonienne à un village serbe aux légendes vampiriques.',           'fr', 2, ['Policier'],               [180],     '2008-01-01', 0],
            ['Les Rivières pourpres',              'Deux enquêteurs affrontent un tueur génial dans les montagnes de Grenoble.',                                           'fr', 3, ['Policier', 'Thriller'],   [181],     '1997-01-01', 0],
            ['L\'Empire des loups',               'Une femme atteinte d\'amnésie est traquée par des hommes liés à la mafia turque d\'Istanbul.',                         'fr', 3, ['Policier', 'Thriller'],   [181],     '2003-01-01', 0],
            ['L.A. Confidential',                  'Trois policiers de Los Angeles enquêtent sur un massacre dans un diner des années 50.',                               'en', 2, ['Policier', 'Thriller'],   [182],     '1990-01-01', 0],
            ['Le Dahlia noir',                     'Un acteur-policier de Los Angeles est obsédé par le meurtre non élucidé d\'Elizabeth Short.',                         'en', 2, ['Policier', 'Thriller'],   [182],     '1987-01-01', 0],
            ['Le Fantôme de l\'Opéra',             'Un mystérieux fantôme hante l\'Opéra de Paris et tombe amoureux d\'une jeune chanteuse.',                             'fr', 3, ['Roman', 'Thriller'],      [215],     '1910-01-01', 0],
            ['Arsène Lupin gentleman-cambrioleur', 'Les aventures du célèbre gentleman-cambrioleur, maître du déguisement et de la ruse.',                                 'fr', 3, ['Policier', 'Roman'],      [216],     '1907-01-01', 0],
            ['Arsène Lupin contre Herlock Sholmès','Le duel épique entre Arsène Lupin et le plus grand détective d\'Angleterre.',                                          'fr', 2, ['Policier', 'Roman'],      [216],     '1908-01-01', 0],
            ['L\'Aiguille creuse',                 'Le secret de l\'Aiguille creuse de Normandie que seul le roi de France et Arsène Lupin connaissent.',                  'fr', 2, ['Policier', 'Roman'],      [216],     '1909-01-01', 0],
            ['Ne le dis à personne',               'Un médecin reçoit un email laissant entendre que sa femme assassinée est toujours vivante.',                          'fr', 3, ['Thriller', 'Policier'],   [212],     '2001-05-14', 0],
            ['Juste un regard',                    'L\'étrange disparition d\'un homme fait ressurgir les mensonges enfouis de toute une famille.',                       'fr', 2, ['Thriller', 'Policier'],   [212],     '2004-01-13', 0],
            ['Et si c\'était vrai',                'Un médecin découvre qu\'une jeune femme dans le coma occupe son appartement sous forme de fantôme.',                  'fr', 3, ['Roman', 'Thriller'],      [213],     '2000-01-01', 0],
            ['Sept jours pour une éternité',       'Un ange et un démon s\'affrontent pour décider si la Terre sera gouvernée par le Bien ou le Mal.',                    'fr', 2, ['Roman', 'Thriller'],      [213],     '2003-01-01', 0],
            ['La Fille de papier',                 'Un personnage de roman sort du livre de son auteur et envahit sa vie réelle.',                                         'fr', 2, ['Roman', 'Thriller'],      [214],     '2010-03-18', 0],
            ['Millénium 2 : La Fille qui rêvait d\'un bidon d\'essence','Lisbeth est accusée de triple meurtre tandis que Blomkvist enquête sur le trafic de femmes.', 'fr', 2, ['Policier', 'Thriller'],   [66],      '2006-01-01', 0],
            ['Millénium 3 : La Reine dans le palais des courants d\'air','Lisbeth est hospitalisée sous garde policière tandis que Blomkvist tente de la disculper.',   'fr', 2, ['Policier', 'Thriller'],   [66],      '2007-01-01', 0],

            // ── Littérature américaine contemporaine ─────────────────────────
            ['Bruit de fond',                      'Une famille américaine ordinaire affronte la mort et la surconsommation dans un campus universitaire.',                'en', 2, ['Roman'],                  [177],     '1985-01-01', 0],
            ['La Trilogie de New York',             'Trois enquêtes imbriquées sur l\'identité, la solitude et le langage dans une New York labyrinthique.',               'en', 2, ['Policier', 'Roman'],      [178],     '1985-01-01', 0],
            ['Moon Palace',                        'Un jeune Américain se retrouve sans ressources et reconstruit sa vie à travers une histoire familiale.',               'en', 2, ['Roman'],                  [178],     '1989-01-01', 0],
            ['La Route',                           'Un père et son fils errent à travers une Amérique post-apocalyptique dans un monde de cendres.',                      'en', 3, ['Roman', 'Science-fiction'], [71],    '2006-09-26', 0],
            ['Méridien de sang',                   'Un adolescent s\'engage dans une bande de scalpers dans le désert américano-mexicain de 1850.',                        'en', 1, ['Roman', 'Histoire'],      [71],      '1985-01-01', 0],
            ['Beloved',                            'Sethe, ancienne esclave, hante sa maison avec le souvenir d\'un acte désespéré pour protéger sa fille.',              'en', 2, ['Roman', 'Histoire'],      [72],      '1987-09-16', 0],
            ['L\'Œil le plus bleu',                'Une petite fille noire rêve d\'avoir les yeux bleus dans le premier roman poignant de Toni Morrison.',                'en', 2, ['Roman'],                  [72],      '1970-01-01', 0],
            ['Le Secret (Le Maître des illusions)', 'Un groupe d\'étudiants en grec ancien se retrouve impliqué dans un meurtre au sein d\'une université du Vermont.',   'en', 2, ['Policier', 'Thriller'],   [211],     '1992-09-16', 0],
            ['L\'Or du fou',                       'Theo Decker survit à l\'attentat qui a tué sa mère au musée et vole un célèbre tableau de Fabritius.',                'en', 3, ['Roman'],                  [211],     '2013-10-22', 0],

            // ── Fantasy et science-fiction (nouveaux titres) ──────────────────
            ['L\'Assassin royal T1 : L\'Apprenti assassin', 'Fitz, bâtard royal au don de communion avec les animaux, est formé comme assassin au service du roi.',    'fr', 2, ['Fantasy'],                 [179],     '1995-01-01', 0],
            ['L\'Assassin royal T2 : L\'Assassin du roi',   'Fitz affronte les Piebald dans un complot visant à renverser la famille royale des Six-Duchés.',           'fr', 2, ['Fantasy'],                 [179],     '1996-01-01', 0],
            ['L\'Assassin royal T3 : La Nef du destin',     'Fitz part en mission secrète pour sauver le prince Royal et les Six-Duchés de l\'invasion des Pie.',      'fr', 1, ['Fantasy'],                 [179],     '1997-01-01', 0],
            ['Neverwhere',                         'Richard Mayhew tombe dans le Londres secret, un monde souterrain parallèle peuplé de créatures fantastiques.',        'en', 2, ['Fantasy'],                 [61],      '1996-09-16', 0],
            ['Coraline',                           'Coraline trouve une porte secrète qui mène dans un monde parallèle où une Autre Mère cherche à la capturer.',         'en', 3, ['Fantasy', 'Horreur'],     [61],      '2002-01-01', 0],
            ['La Danse avec les dragons',          'Daenerys tente de gouverner Meereen tandis que Jon Snow affronte la menace des Marcheurs Blancs au nord.',            'en', 1, ['Fantasy'],                 [58],      '2011-07-12', 0],
            ['La Peur du sage',                    'Kvothe quitte l\'université et apprend la magie des vents auprès des Adem dans le deuxième tome de la trilogie.',    'en', 2, ['Fantasy'],                 [59],      '2011-03-01', 0],
            ['La Guerre éternelle',                'Des soldats envoyés en guerre interstellaire reviennent des décennies après leur départ à cause de la relativité.',   'en', 2, ['Science-fiction'],         [53],      '1974-01-01', 0],
            ['Les Dépossédés',                     'Le Guin compare deux sociétés planétaires, l\'une capitaliste, l\'autre anarchiste, à travers un physicien.',         'en', 2, ['Science-fiction', 'Philosophie'], [55], '1974-01-01', 0],
            ['Le Dit de la Terre',                 'Le Guin explore une planète dont les habitants changent de sexe selon les cycles lunaires.',                           'en', 2, ['Science-fiction'],         [55],      '1969-01-01', 0],
            ['Piranesi',                           'Un homme vit seul dans un palais infini dont les couloirs sont remplis de statues et les caves d\'océans.',            'en', 2, ['Fantasy'],                 [61],      '2020-09-15', 0],
            ['Elantris',                           'Un prince transformé par la magie défaillante d\'Elantris cherche à comprendre ce qui a corrompu la ville sacrée.',   'en', 2, ['Fantasy'],                 [126],     '2005-04-21', 0],
            ['L\'Acier trempé',                    'Sanderson imagine un monde en lutte contre un dieu immortel : une équipe de voleurs prépare le heist ultime.',         'en', 1, ['Fantasy'],                 [126],     '2008-01-01', 0],
            ['Les Chroniques du Disque-Monde : Mortimer', 'La Mort prend un apprenti nommé Mortimer dans ce volume emblématique du Disque-Monde.',                       'fr', 2, ['Fantasy'],                 [62],      '1987-01-01', 0],
            ['Wyrd Sisters',                       'Trois sorcières du Disque-Monde se mêlent des affaires d\'un royaume et rendent hommage à Shakespeare.',             'en', 2, ['Fantasy'],                 [62],      '1988-01-01', 0],
            ['Les androïdes rêvent-ils de moutons électriques (2e ex)',
                                                   'Second exemplaire de l\'œuvre qui a inspiré Blade Runner pour répondre à la forte demande.',                          'fr', 2, ['Science-fiction'],         [26],      '1968-01-01', 0],
            ['L\'Homme dans le château',           'Philip K. Dick imagine un monde alternatif où l\'Axe a gagné la Seconde Guerre mondiale.',                            'fr', 2, ['Science-fiction', 'Histoire'], [26], '1962-01-01', 0],
            ['Minority Report',                    'Dick imagine une police qui arrête les criminels avant qu\'ils n\'aient commis leur crime.',                           'fr', 2, ['Science-fiction'],         [26],      '1956-01-01', 0],
            ['Le Cycle de Fondation T4 : Fondation foudroyée', 'Asimov enrichit son cycle : l\'historien Trevize cherche la deuxième fondation dans toute la galaxie.',  'en', 1, ['Science-fiction'],         [4],       '1982-01-01', 0],
            ['Les Robots de l\'aube',              'Elijah Baley enquête sur un crime impossible commis sur la planète Aurora, entouré de robots très humains.',          'en', 1, ['Science-fiction'],         [4],       '1983-01-01', 0],
            ['Contact',                            'Carl Sagan imagine le premier contact entre l\'humanité et une intelligence extraterrestre.',                          'en', 2, ['Science-fiction'],         [],        '1985-09-01', 0],
            ['Hypérion T2 : La Chute d\'Hypérion','Les pèlerins affrontent le Gritche tandis que la galaxie plonge dans la guerre dans la suite du premier tome.',        'en', 1, ['Science-fiction'],         [50],      '1990-01-01', 0],
            ['L\'Épée de vérité T1 : La Première leçon du sorcier', 'Un homme ordinaire découvre qu\'il est le Chercheur de Vérité et doit sauver le monde de la tyrannie.', 'en', 2, ['Fantasy'],           [],        '1994-01-01', 0],

            // ── Poésie (nouveaux titres) ──────────────────────────────────────
            ['Illuminations',                      'Rimbaud assemble des poèmes en prose d\'une modernité absolue, visions colorées et hallucinées.',                     'fr', 1, ['Poésie'],                 [151],     '1886-01-01', 0],
            ['Romances sans paroles',              'Verlaine compose des poèmes musicaux et mélancoliques inspirés par sa vie itinérante avec Rimbaud.',                  'fr', 1, ['Poésie'],                 [152],     '1874-01-01', 0],
            ['Sagesse',                            'Verlaine, converti après sa prison, exprime sa foi retrouvée et sa paix intérieure retrouvée.',                       'fr', 1, ['Poésie'],                 [152],     '1881-01-01', 0],
            ['Les Contemplations',                 'Hugo pleure sa fille Léopoldine et médite sur l\'amour, la mort et l\'infini dans ce recueil majeur.',                'fr', 2, ['Poésie'],                 [0],       '1856-01-01', 0],
            ['La Légende des siècles',             'Hugo compose une épopée poétique de l\'histoire humaine, de la Création au futur idéal.',                             'fr', 1, ['Poésie', 'Histoire'],     [0],       '1859-01-01', 0],
            ['Spleen de Paris',                    'Baudelaire invente le poème en prose avec ces petits tableaux urbains de la modernité parisienne.',                   'fr', 1, ['Poésie'],                 [153],     '1869-01-01', 0],

            // ── Développement personnel & bien-être ──────────────────────────
            ['Flow : l\'expérience optimale',      'Csikszentmihalyi explore les conditions psychologiques du bonheur et de l\'engagement total dans l\'activité.',       'fr', 2, ['Développement personnel'], [],       '1990-01-01', 0],
            ['L\'Intelligence émotionnelle',       'Daniel Goleman démontre l\'importance de l\'IE dans la réussite personnelle et professionnelle.',                     'fr', 3, ['Développement personnel'], [],       '1995-01-01', 0],
            ['Quiet : la force des introvertis',   'Susan Cain réhabilite les introvertis et montre leur contribution exceptionnelle à la société et aux entreprises.',   'fr', 2, ['Développement personnel'], [],       '2012-01-24', 0],
            ['Grit : la passion de l\'excellence', 'Angela Duckworth prouve que la persévérance compte plus que le talent pour atteindre l\'excellence.',                'en', 2, ['Développement personnel'], [],       '2016-05-03', 0],
            ['La Puissance des habitudes',         'Charles Duhigg analyse le mécanisme des habitudes et explique comment les transformer.',                              'fr', 3, ['Développement personnel'], [],       '2012-02-28', 0],
            ['Mindset',                            'Carol Dweck distingue le « fixed mindset » du « growth mindset » et leur impact sur la réussite.',                   'en', 2, ['Développement personnel'], [],       '2006-02-28', 0],
            ['Le Pouvoir du moment présent',       'Eckhart Tolle guide vers une conscience libérée du mental et ancrée dans l\'instant présent.',                       'fr', 3, ['Développement personnel', 'Philosophie'], [], '1997-01-01', 0],
            ['Deep Work (Travail en profondeur)',   'Cal Newport argumente que la capacité de se concentrer sans distraction est la superpuissance du XXIe siècle.',      'en', 2, ['Développement personnel'], [97],     '2016-01-05', 0],
            ['The Lean Startup',                   'Eric Ries explique comment les startups peuvent réussir grâce à l\'apprentissage validé et à l\'itération rapide.',  'en', 2, ['Économie'],               [94],      '2011-09-13', 0],
            ['Sprint',                             'Jake Knapp décrit la méthode Google Ventures pour résoudre en cinq jours des problèmes importants.',                 'en', 2, ['Économie', 'Développement personnel'], [], '2016-03-08', 0],

            // ── Informatique (nouveaux titres) ────────────────────────────────
            ['Clean Architecture',                 'Robert C. Martin explique comment structurer les systèmes logiciels pour les rendre durables et testables.',          'en', 2, ['Informatique'],           [105],     '2017-09-12', 0],
            ['The Phoenix Project',                'Un roman sur la transformation d\'une entreprise grâce aux principes DevOps racontés à travers une fiction captivante.','en', 2, ['Informatique'],         [],        '2013-01-10', 0],
            ['Continuous Delivery',                'Humble et Farley décrivent comment livrer des logiciels rapidement, de manière fiable et répétable.',                 'en', 1, ['Informatique'],           [],        '2010-07-27', 0],
            ['JavaScript: The Good Parts',         'Douglas Crockford extrait l\'essence de JavaScript en ignorant ses parties les plus problématiques.',                 'en', 2, ['Informatique'],           [],        '2008-05-01', 0],
            ['Éloquent JavaScript',                'Marijn Haverbeke propose une initiation profonde à JavaScript et à la programmation fonctionnelle.',                  'en', 2, ['Informatique'],           [],        '2011-01-01', 0],
            ['Apprentissage automatique',          'Aurélien Géron présente les techniques de Machine Learning avec Python et scikit-learn.',                              'fr', 1, ['Informatique'],           [],        '2017-03-28', 0],
            ['Pro Git',                            'Scott Chacon explique Git en profondeur, de la configuration de base aux workflows avancés.',                         'en', 1, ['Informatique'],           [],        '2014-11-01', 0],
            ['Docker Deep Dive',                   'Nigel Poulton présente Docker et la conteneurisation de manière accessible et pratique.',                              'en', 1, ['Informatique'],           [],        '2016-01-01', 0],

            // ── Histoire & essais ─────────────────────────────────────────────
            ['Freakonomics',                       'Deux économistes révèlent les mécanismes cachés derrière des phénomènes sociaux inattendus.',                         'en', 3, ['Économie', 'Développement personnel'], [], '2005-04-12', 0],
            ['Le Capital au XXIe siècle',          'Piketty analyse la concentration des richesses et les inégalités de revenu depuis le XIXe siècle.',                  'fr', 2, ['Économie'],               [],        '2013-08-29', 0],
            ['Pourquoi les nations échouent',      'Acemoglu et Robinson expliquent la richesse et la pauvreté des nations par les institutions politiques et économiques.','fr', 1, ['Économie', 'Histoire'], [],       '2012-03-20', 0],
            ['La Guerre du Péloponnèse',           'Thucydide retrace la guerre entre Athènes et Sparte dans le premier ouvrage d\'histoire scientifique occidental.',   'fr', 1, ['Histoire'],               [],        '0411-01-01', 0],
            ['La Cité de Dieu',                    'Saint Augustin répond au sac de Rome en opposant la cité terrestre à la cité de Dieu dans une œuvre monumentale.',   'fr', 1, ['Philosophie'],             [],        '0413-01-01', 0],
            ['Discours de la servitude volontaire','La Boétie pose la question fondamentale : pourquoi le peuple obéit-il volontairement à la tyrannie ?',                'fr', 1, ['Philosophie'],             [],        '1576-01-01', 0],
            ['Les Essais',                         'Montaigne invente le genre de l\'essai en explorant librement ses pensées sur la mort, l\'amitié et la condition humaine.','fr', 1, ['Philosophie'],       [],        '1580-01-01', 0],
            ['L\'Esprit des Lumières',             'Tzvetan Todorov retrace les grandes idées du XVIIIe siècle et leur héritage dans la modernité.',                     'fr', 1, ['Philosophie', 'Histoire'], [],       '2006-01-01', 0],
            ['Une brève histoire du temps',        'Stephen Hawking vulgarise les grands mystères de la physique : trous noirs, Big Bang et nature du temps.',            'fr', 3, ['Histoire', 'Philosophie'], [],      '1988-04-01', 0],
            ['La Structure des révolutions scientifiques', 'Thomas Kuhn introduit le concept de paradigme et explique comment la science progresse par ruptures.',       'fr', 1, ['Philosophie'],             [],        '1962-01-01', 0],

            // ── Romans classiques oubliés ─────────────────────────────────────
            ['Silas Marner',                       'Un tisserand solitaire et avare est transformé par l\'arrivée mystérieuse d\'une petite fille.',                      'en', 2, ['Roman'],                  [37],      '1861-03-02', 0],
            ['L\'Agent secret',                    'Conrad décrit l\'infiltration d\'un groupe anarchiste par les services secrets dans le Londres édouardien.',           'en', 1, ['Roman', 'Policier'],     [],        '1907-01-01', 0],
            ['Au cœur des ténèbres',               'Marlow remonte le Congo pour retrouver l\'énigmatique Kurtz, trader qui s\'est fait dieu des indigènes.',             'en', 2, ['Roman'],                  [],        '1899-01-01', 0],
            ['Kim',                                'Un orphelin irlandais grandit dans l\'Inde coloniale et est recruté pour le Grand Jeu d\'espionnage britannique.',    'en', 1, ['Roman', 'Histoire'],      [],        '1901-01-01', 0],
            ['Barbe bleue',                        'Balzac revisité ou Perrault ? Un peintre séquestre ses femmes dans son atelier : qui est l\'assassin ?',              'fr', 2, ['Roman'],                  [],        '1799-01-01', 0],
            ['Adolphe',                            'Benjamin Constant décrit avec lucidité la fin d\'un amour et l\'incapacité d\'un homme à s\'engager vraiment.',      'fr', 1, ['Roman'],                  [],        '1816-01-01', 0],
            ['Paul et Virginie',                   'Bernardin de Saint-Pierre raconte l\'amour pur de deux enfants élevés dans la nature à l\'Île de France.',            'fr', 1, ['Roman', 'Jeunesse'],     [],        '1788-01-01', 0],
            ['Manon Lescaut',                      'Le chevalier des Grieux sacrifie tout pour une femme légère et inconstante dans l\'Ancien Régime.',                   'fr', 2, ['Roman'],                  [],        '1731-01-01', 0],
            ['La Princesse de Clèves',             'Dans la cour d\'Henri II, une jeune femme mariée lutte contre une passion interdite pour le duc de Nemours.',         'fr', 2, ['Roman'],                  [],        '1678-01-01', 0],
            ['Gil Blas',                           'Lesage décrit les aventures picaresques d\'un jeune homme traversant toute la société espagnole du XVIIIe.',          'fr', 1, ['Roman'],                  [],        '1715-01-01', 0],
        ];

        $uploadDir = $this->projectDir . '/public/uploads/books';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $client = HttpClient::create(['timeout' => 10]);
        $books  = [];
        $downloaded = 0;
        $skipped    = 0;

        foreach ($booksData as [$title, $desc, $langCode, $stock, $catNames, $authorIdxs, $date, $coverId]) {
            $book = new Book();
            $book->setTitle($title)
                 ->setDescription($desc)
                 ->setStock($stock)
                 ->setLanguage($languages[$langCode] ?? null)
                 ->setPublishedAt(new \DateTime($date));

            foreach ($catNames as $cn) {
                if (isset($categories[$cn])) $book->addCategory($categories[$cn]);
            }
            foreach ($authorIdxs as $idx) {
                if (isset($authors[$idx])) $book->addAuthor($authors[$idx]);
            }

            if ($coverId) {
                $filename = 'cover_' . $coverId . '.jpg';
                $filepath = $uploadDir . '/' . $filename;

                if (!file_exists($filepath)) {
                    try {
                        $url      = 'https://covers.openlibrary.org/b/id/' . $coverId . '-M.jpg';
                        $response = $client->request('GET', $url);
                        $content  = $response->getContent();
                        if (strlen($content) > 1000) {
                            file_put_contents($filepath, $content);
                            $book->setCoverImage($filename);
                            $downloaded++;
                            echo "  ✓ $title\n";
                        }
                    } catch (\Exception $e) {
                        echo "  ✗ Erreur image : $title\n";
                    }
                } else {
                    $book->setCoverImage($filename);
                    $skipped++;
                }
            }

            $manager->persist($book);
            $books[] = $book;
        }

        // ── Réservations exemples ─────────────────────────────────────────────
        foreach ([[$books[0], '+1 day', '+15 days'], [$books[14], '+3 days', '+20 days'], [$books[50], '+5 days', '+25 days']] as [$b, $start, $end]) {
            $res = new Reservation();
            $res->setUser($user)->setBook($b)
                ->setStartDate(new \DateTime($start))->setEndDate(new \DateTime($end))
                ->setStatus(Reservation::STATUS_PENDING);
            $manager->persist($res);
        }

        // ── Commentaires approuvés ────────────────────────────────────────────
        foreach ([
            [$books[0],   5, 'Un chef-d\'œuvre absolu, incontournable de la littérature française !'],
            [$books[48],  5, 'Visionnaire et glaçant. À lire absolument pour comprendre le monde actuel.'],
            [$books[16],  5, 'Court mais profond. Touche les adultes autant que les enfants.'],
            [$books[51],  4, 'Un univers immense et fascinant, Dune est la bible de la science-fiction.'],
            [$books[71],  5, 'L\'œuvre maîtresse de Tolkien, un voyage inoubliable.'],
            [$books[73],  4, 'Harry Potter a bercé mon enfance, toujours aussi magique à relire.'],
            [$books[97],  5, 'La meilleure introduction à la pensée algorithmique.'],
            [$books[120], 4, 'Orgueil et Préjugés reste une leçon de psychologie amoureuse intemporelle.'],
        ] as [$book, $rating, $content]) {
            if (!isset($book)) continue;
            $comment = new Comment();
            $comment->setUser($user)->setBook($book)
                    ->setContent($content)->setRating($rating)->setIsApproved(true);
            $manager->persist($comment);
        }

        $manager->flush();

        $total = count($books);
        echo "\n✅ $total livres chargés ($downloaded nouvelles couvertures, $skipped déjà présentes).\n";
    }
}
