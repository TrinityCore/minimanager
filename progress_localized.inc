        $is_es = (isset($_COOKIE['lang']) && stripos($_COOKIE['lang'], 'spanish') !== false)
              || (isset($lang) && stripos($lang, 'spanish') !== false);

        $txt = $is_es ? array(
            'cls_d_title' => 'Mazmorras Clásicas (Vanilla)',
            'cls_r_title' => 'Bandas Clásicas (Vanilla)',
            'tbc_d_title' => 'Mazmorras de Terrallende (TBC)',
            'tbc_r_title' => 'Bandas de Terrallende (TBC)',
            'wot_d_title' => 'Mazmorras de Rasganorte (WotLK)',
            'wot_r_title' => 'Bandas de Rasganorte (10 / 25)',
            'completed'   => 'Completadas',
            'modes'       => 'Modalidades',
            'dungeons'    => 'Mazmorras',
            'raids'       => 'Bandas'
        ) : array(
            'cls_d_title' => 'Classic Dungeons (Vanilla)',
            'cls_r_title' => 'Classic Raids (Vanilla)',
            'tbc_d_title' => 'Outland Dungeons (TBC)',
            'tbc_r_title' => 'Outland Raids (TBC)',
            'wot_d_title' => 'Northrend Dungeons (WotLK)',
            'wot_r_title' => 'Northrend Raids (10 / 25)',
            'completed'   => 'Completed',
            'modes'       => 'Modes',
            'dungeons'    => 'Dungeons',
            'raids'       => 'Raids'
        );

        $classic_dungeons = array(
            array(628,  $is_es ? "Sima Ígnea" : "Ragefire Chasm"),
            array(629,  $is_es ? "Cuevas de los Lamentos" : "Wailing Caverns"),
            array(630,  $is_es ? "Las Minas de la Muerte" : "The Deadmines"),
            array(631,  $is_es ? "Castillo de Colmillo Oscuro" : "Shadowfang Keep"),
            array(632,  $is_es ? "Cavernas de Brazanegra" : "Blackfathom Deeps"),
            array(633,  $is_es ? "Mazmorras de Ventormenta" : "The Stockade"),
            array(634,  "Gnomeregan"),
            array(635,  $is_es ? "Horado Rajacieno" : "Razorfen Kraul"),
            array(636,  $is_es ? "Zahúrda Rajacieno" : "Razorfen Downs"),
            array(637,  $is_es ? "Monasterio Escarlata" : "Scarlet Monastery"),
            array(638,  "Uldaman"),
            array(639,  "Zul'Farrak"),
            array(640,  "Maraudon"),
            array(641,  $is_es ? "Templo Sumergido" : "Sunken Temple"),
            array(642,  $is_es ? "Profundidades de Roca Negra" : "Blackrock Depths"),
            array(643,  $is_es ? "Cumbre de Roca Negra Inferior" : "Lower Blackrock Spire"),
            array(1307, $is_es ? "Cumbre de Roca Negra Superior" : "Upper Blackrock Spire"),
            array(644,  $is_es ? "La Masacre" : "Dire Maul"),
            array(645,  "Scholomance"),
            array(646,  "Stratholme")
        );

        $classic_raids = array(
            array(688, "Zul'Gurub"),
            array(689, $is_es ? "Ruinas de Ahn'Qiraj" : "Ruins of Ahn'Qiraj"),
            array(686, $is_es ? "Núcleo de Magma" : "Molten Core"),
            array(685, $is_es ? "Guarida de Alanegra" : "Blackwing Lair"),
            array(687, $is_es ? "Templo de Ahn'Qiraj" : "Temple of Ahn'Qiraj"),
            array(684, $is_es ? "Guarida de Onyxia (Nv. 60)" : "Onyxia's Lair (Lv. 60)")
        );

        $tbc_dungeons = array(
            array(647, 667, $is_es ? "Murallas del Fuego Infernal" : "Hellfire Ramparts"),
            array(648, 668, $is_es ? "El Horno de Sangre" : "The Blood Furnace"),
            array(651, 678, $is_es ? "Las Salas Arrasadas" : "The Shattered Halls"),
            array(649, 669, $is_es ? "Recinto de los Esclavos" : "The Slave Pens"),
            array(650, 670, $is_es ? "La Sotiénaga" : "The Underbog"),
            array(652, 671, $is_es ? "La Cámara de Vapor" : "The Steamvault"),
            array(653, 672, $is_es ? "Tumbas de Maná" : "Mana-Tombs"),
            array(654, 673, $is_es ? "Criptas Auchenai" : "Auchenai Crypts"),
            array(656, 674, $is_es ? "Salas Sethekk" : "Sethekk Halls"),
            array(657, 675, $is_es ? "Laberinto de las Sombras" : "Shadow Labyrinth"),
            array(655, 676, $is_es ? "Antiguas Laderas de Trabalomas" : "Old Hillsbrad Foothills"),
            array(658, 677, $is_es ? "La Apertura del Portal Oscuro" : "The Black Morass"),
            array(659, 679, $is_es ? "El Mechanar" : "The Mechanar"),
            array(660, 680, $is_es ? "El Invernáculo" : "The Botanica"),
            array(661, 681, $is_es ? "El Arcatraz" : "The Arcatraz"),
            array(662, 682, $is_es ? "Bancal del Magister" : "Magisters' Terrace")
        );

        $tbc_raids = array(
            array(690, "Karazhan"),
            array(692, $is_es ? "Guarida de Gruul" : "Gruul's Lair"),
            array(693, $is_es ? "Guarida de Magtheridon" : "Magtheridon's Lair"),
            array(694, $is_es ? "Caverna Santuario Serpiente" : "Serpentshrine Cavern"),
            array(696, $is_es ? "El Ojo (Castillo de la Tempestad)" : "The Eye (Tempest Keep)"),
            array(695, $is_es ? "La Batalla del Monte Hyjal" : "The Battle for Mount Hyjal"),
            array(697, $is_es ? "El Templo Oscuro" : "Black Temple"),
            array(698, $is_es ? "Meseta de La Fuente del Sol" : "Sunwell Plateau")
        );

        $wotlk_dungeons = array(
            array(477,  489,  $is_es ? "Fortaleza de Utgarde" : "Utgarde Keep"),
            array(478,  490,  $is_es ? "El Nexo" : "The Nexus"),
            array(480,  491,  "Azjol-Nerub"),
            array(481,  492,  $is_es ? "Ahn'kahet: El Antiguo Reino" : "Ahn'kahet: The Old Kingdom"),
            array(482,  493,  $is_es ? "Fortaleza de Drak'Tharon" : "Drak'Tharon Keep"),
            array(483,  494,  $is_es ? "El Bastión Violeta" : "The Violet Hold"),
            array(484,  495,  "Gundrak"),
            array(485,  496,  $is_es ? "Cámaras de Piedra" : "Halls of Stone"),
            array(486,  497,  $is_es ? "Cámaras de Relámpagos" : "Halls of Lightning"),
            array(487,  498,  $is_es ? "El Oculus" : "The Oculus"),
            array(488,  499,  $is_es ? "Pináculo de Utgarde" : "Utgarde Pinnacle"),
            array(479,  500,  $is_es ? "La Matanza de Stratholme" : "The Culling of Stratholme"),
            array(4296, 4298, $is_es ? "Prueba del Campeón" : "Trial of the Champion"),
            array(4516, 4519, $is_es ? "La Forja de Almas" : "The Forge of Souls"),
            array(4517, 4520, $is_es ? "Foso de Saron" : "Pit of Saron"),
            array(4518, 4521, $is_es ? "Cámaras de Reflexión" : "Halls of Reflection")
        );

        $wotlk_raids = array(
            array(576,  577,  "Naxxramas"),
            array(1876, 625,  $is_es ? "El Sagrario Obsidiana" : "The Obsidian Sanctum"),
            array(622,  623,  $is_es ? "El Ojo de la Eternidad" : "The Eye of Eternity"),
            array(1722, 1721, $is_es ? "La Cámara de Archavon" : "Vault of Archavon"),
            array(2894, 2895, "Ulduar"),
            array(3917, 3916, $is_es ? "Prueba del Cruzado" : "Trial of the Crusader"),
            array(4396, 4397, $is_es ? "La Guarida de Onyxia" : "Onyxia's Lair"),
            array(4532, 4608, $is_es ? "Ciudadela de la Corona de Hielo" : "Icecrown Citadel"),
            array(4817, 4815, $is_es ? "El Sagrario Rubí" : "The Ruby Sanctum")
        );
