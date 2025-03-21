<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Builder\Entity;

use App\Btw\Wahlkreise;
use App\Dto\WahlkreisType;
use App\Entity\Wahlkreis;
use Symfony\Component\Uid\Ulid;

class WahlkreisBuilder
{
    private const array WAHLKREISE = [
        ['number' => 1, 'name' => 'Flensburg – Schleswig', 'state' => 'Schleswig-Holstein'],
        ['number' => 2, 'name' => 'Nordfriesland – Dithmarschen Nord', 'state' => 'Schleswig-Holstein'],
        ['number' => 3, 'name' => 'Steinburg – Dithmarschen Süd', 'state' => 'Schleswig-Holstein'],
        ['number' => 4, 'name' => 'Rendsburg-Eckernförde', 'state' => 'Schleswig-Holstein'],
        ['number' => 5, 'name' => 'Kiel', 'state' => 'Schleswig-Holstein'],
        ['number' => 6, 'name' => 'Plön – Neumünster', 'state' => 'Schleswig-Holstein'],
        ['number' => 7, 'name' => 'Pinneberg', 'state' => 'Schleswig-Holstein'],
        ['number' => 8, 'name' => 'Segeberg – Stormarn-Mitte', 'state' => 'Schleswig-Holstein'],
        ['number' => 9, 'name' => 'Ostholstein – Stormarn-Nord', 'state' => 'Schleswig-Holstein'],
        ['number' => 10, 'name' => 'Herzogtum Lauenburg – Stormarn-Süd', 'state' => 'Schleswig-Holstein'],
        ['number' => 11, 'name' => 'Lübeck', 'state' => 'Schleswig-Holstein'],
        ['number' => 12, 'name' => 'Schwerin – Ludwigslust-Parchim I – Nordwestmecklenburg I', 'state' => 'Mecklenburg-Vorpommern'],
        ['number' => 13, 'name' => 'Ludwigslust-Parchim II – Nordwestmecklenburg II – Landkreis Rostock I', 'state' => 'Mecklenburg-Vorpommern'],
        ['number' => 14, 'name' => 'Rostock – Landkreis Rostock II', 'state' => 'Mecklenburg-Vorpommern'],
        ['number' => 15, 'name' => 'Vorpommern-Rügen – Vorpommern-Greifswald I', 'state' => 'Mecklenburg-Vorpommern'],
        ['number' => 16, 'name' => 'Mecklenburgische Seenplatte I – Vorpommern-Greifswald II', 'state' => 'Mecklenburg-Vorpommern'],
        ['number' => 17, 'name' => 'Mecklenburgische Seenplatte II – Landkreis Rostock III', 'state' => 'Mecklenburg-Vorpommern'],
        ['number' => 18, 'name' => 'Hamburg-Mitte', 'state' => 'Hamburg'],
        ['number' => 19, 'name' => 'Hamburg-Altona', 'state' => 'Hamburg'],
        ['number' => 20, 'name' => 'Hamburg-Eimsbüttel', 'state' => 'Hamburg'],
        ['number' => 21, 'name' => 'Hamburg-Nord', 'state' => 'Hamburg'],
        ['number' => 22, 'name' => 'Hamburg-Wandsbek', 'state' => 'Hamburg'],
        ['number' => 23, 'name' => 'Hamburg-Bergedorf – Harburg', 'state' => 'Hamburg'],
        ['number' => 24, 'name' => 'Aurich – Emden', 'state' => 'Niedersachsen'],
        ['number' => 25, 'name' => 'Unterems', 'state' => 'Niedersachsen'],
        ['number' => 26, 'name' => 'Friesland – Wilhelmshaven – Wittmund', 'state' => 'Niedersachsen'],
        ['number' => 27, 'name' => 'Oldenburg – Ammerland', 'state' => 'Niedersachsen'],
        ['number' => 28, 'name' => 'Delmenhorst – Wesermarsch – Oldenburg-Land', 'state' => 'Niedersachsen'],
        ['number' => 29, 'name' => 'Cuxhaven – Stade II', 'state' => 'Niedersachsen'],
        ['number' => 30, 'name' => 'Stade I – Rotenburg II', 'state' => 'Niedersachsen'],
        ['number' => 31, 'name' => 'Mittelems', 'state' => 'Niedersachsen'],
        ['number' => 32, 'name' => 'Cloppenburg – Vechta', 'state' => 'Niedersachsen'],
        ['number' => 33, 'name' => 'Diepholz – Nienburg I', 'state' => 'Niedersachsen'],
        ['number' => 34, 'name' => 'Osterholz – Verden', 'state' => 'Niedersachsen'],
        ['number' => 35, 'name' => 'Rotenburg I – Heidekreis', 'state' => 'Niedersachsen'],
        ['number' => 36, 'name' => 'Harburg', 'state' => 'Niedersachsen'],
        ['number' => 37, 'name' => 'Lüchow-Dannenberg – Lüneburg', 'state' => 'Niedersachsen'],
        ['number' => 38, 'name' => 'Osnabrück-Land', 'state' => 'Niedersachsen'],
        ['number' => 39, 'name' => 'Stadt Osnabrück', 'state' => 'Niedersachsen'],
        ['number' => 40, 'name' => 'Nienburg II – Schaumburg', 'state' => 'Niedersachsen'],
        ['number' => 41, 'name' => 'Stadt Hannover I', 'state' => 'Niedersachsen'],
        ['number' => 42, 'name' => 'Stadt Hannover II', 'state' => 'Niedersachsen'],
        ['number' => 43, 'name' => 'Hannover-Land I', 'state' => 'Niedersachsen'],
        ['number' => 44, 'name' => 'Celle – Uelzen', 'state' => 'Niedersachsen'],
        ['number' => 45, 'name' => 'Gifhorn – Peine', 'state' => 'Niedersachsen'],
        ['number' => 46, 'name' => 'Hameln-Pyrmont – Holzminden', 'state' => 'Niedersachsen'],
        ['number' => 47, 'name' => 'Hannover-Land II', 'state' => 'Niedersachsen'],
        ['number' => 48, 'name' => 'Hildesheim', 'state' => 'Niedersachsen'],
        ['number' => 49, 'name' => 'Salzgitter – Wolfenbüttel', 'state' => 'Niedersachsen'],
        ['number' => 50, 'name' => 'Braunschweig', 'state' => 'Niedersachsen'],
        ['number' => 51, 'name' => 'Helmstedt – Wolfsburg', 'state' => 'Niedersachsen'],
        ['number' => 52, 'name' => 'Goslar – Northeim – Göttingen II', 'state' => 'Niedersachsen'],
        ['number' => 53, 'name' => 'Göttingen I', 'state' => 'Niedersachsen'],
        ['number' => 54, 'name' => 'Bremen I', 'state' => 'Bremen'],
        ['number' => 55, 'name' => 'Bremen II – Bremerhaven', 'state' => 'Bremen'],
        ['number' => 56, 'name' => 'Prignitz – Ostprignitz-Ruppin – Havelland I', 'state' => 'Brandenburg'],
        ['number' => 57, 'name' => 'Uckermark – Barnim I', 'state' => 'Brandenburg'],
        ['number' => 58, 'name' => 'Oberhavel – Havelland II', 'state' => 'Brandenburg'],
        ['number' => 59, 'name' => 'Märkisch-Oderland – Barnim II', 'state' => 'Brandenburg'],
        ['number' => 60, 'name' => 'Brandenburg an der Havel – Potsdam-Mittelmark I – Havelland III – Teltow-Fläming I', 'state' => 'Brandenburg'],
        ['number' => 61, 'name' => 'Potsdam – Potsdam-Mittelmark II – Teltow-Fläming II', 'state' => 'Brandenburg'],
        ['number' => 62, 'name' => 'Dahme-Spreewald – Teltow-Fläming III', 'state' => 'Brandenburg'],
        ['number' => 63, 'name' => 'Frankfurt (Oder) – Oder-Spree', 'state' => 'Brandenburg'],
        ['number' => 64, 'name' => 'Cottbus – Spree-Neiße', 'state' => 'Brandenburg'],
        ['number' => 65, 'name' => 'Elbe-Elster – Oberspreewald-Lausitz', 'state' => 'Brandenburg'],
        ['number' => 66, 'name' => 'Altmark – Jerichower Land', 'state' => 'Sachsen-Anhalt'],
        ['number' => 67, 'name' => 'Börde – Salzlandkreis', 'state' => 'Sachsen-Anhalt'],
        ['number' => 68, 'name' => 'Harz', 'state' => 'Sachsen-Anhalt'],
        ['number' => 69, 'name' => 'Magdeburg', 'state' => 'Sachsen-Anhalt'],
        ['number' => 70, 'name' => 'Anhalt – Dessau – Wittenberg', 'state' => 'Sachsen-Anhalt'],
        ['number' => 71, 'name' => 'Halle', 'state' => 'Sachsen-Anhalt'],
        ['number' => 72, 'name' => 'Burgenland – Saalekreis', 'state' => 'Sachsen-Anhalt'],
        ['number' => 73, 'name' => 'Mansfeld', 'state' => 'Sachsen-Anhalt'],
        ['number' => 74, 'name' => 'Berlin-Mitte', 'state' => 'Berlin'],
        ['number' => 75, 'name' => 'Berlin-Pankow', 'state' => 'Berlin'],
        ['number' => 76, 'name' => 'Berlin-Reinickendorf', 'state' => 'Berlin'],
        ['number' => 77, 'name' => 'Berlin-Spandau – Charlottenburg Nord', 'state' => 'Berlin'],
        ['number' => 78, 'name' => 'Berlin-Steglitz-Zehlendorf', 'state' => 'Berlin'],
        ['number' => 79, 'name' => 'Berlin-Charlottenburg-Wilmersdorf', 'state' => 'Berlin'],
        ['number' => 80, 'name' => 'Berlin-Tempelhof-Schöneberg', 'state' => 'Berlin'],
        ['number' => 81, 'name' => 'Berlin-Neukölln', 'state' => 'Berlin'],
        ['number' => 82, 'name' => 'Berlin-Friedrichshain-Kreuzberg – Prenzlauer Berg Ost', 'state' => 'Berlin'],
        ['number' => 83, 'name' => 'Berlin-Treptow-Köpenick', 'state' => 'Berlin'],
        ['number' => 84, 'name' => 'Berlin-Marzahn-Hellersdorf', 'state' => 'Berlin'],
        ['number' => 85, 'name' => 'Berlin-Lichtenberg', 'state' => 'Berlin'],
        ['number' => 86, 'name' => 'Aachen I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 87, 'name' => 'Aachen II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 88, 'name' => 'Heinsberg', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 89, 'name' => 'Düren', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 90, 'name' => 'Rhein-Erft-Kreis I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 91, 'name' => 'Euskirchen – Rhein-Erft-Kreis II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 92, 'name' => 'Köln I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 93, 'name' => 'Köln II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 94, 'name' => 'Köln III', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 95, 'name' => 'Bonn', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 96, 'name' => 'Rhein-Sieg-Kreis I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 97, 'name' => 'Rhein-Sieg-Kreis II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 98, 'name' => 'Oberbergischer Kreis', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 99, 'name' => 'Rheinisch-Bergischer Kreis', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 100, 'name' => 'Leverkusen – Köln IV', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 101, 'name' => 'Wuppertal I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 102, 'name' => 'Solingen – Remscheid – Wuppertal II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 103, 'name' => 'Mettmann I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 104, 'name' => 'Mettmann II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 105, 'name' => 'Düsseldorf I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 106, 'name' => 'Düsseldorf II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 107, 'name' => 'Neuss I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 108, 'name' => 'Mönchengladbach', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 109, 'name' => 'Krefeld I – Neuss II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 110, 'name' => 'Viersen', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 111, 'name' => 'Kleve', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 112, 'name' => 'Wesel I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 113, 'name' => 'Krefeld II – Wesel II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 114, 'name' => 'Duisburg I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 115, 'name' => 'Duisburg II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 116, 'name' => 'Oberhausen – Wesel III', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 117, 'name' => 'Mülheim – Essen I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 118, 'name' => 'Essen II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 119, 'name' => 'Essen III', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 120, 'name' => 'Recklinghausen I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 121, 'name' => 'Recklinghausen II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 122, 'name' => 'Gelsenkirchen', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 123, 'name' => 'Steinfurt I – Borken I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 124, 'name' => 'Bottrop – Recklinghausen III', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 125, 'name' => 'Borken II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 126, 'name' => 'Coesfeld – Steinfurt II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 127, 'name' => 'Steinfurt III', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 128, 'name' => 'Münster', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 129, 'name' => 'Warendorf', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 130, 'name' => 'Gütersloh I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 131, 'name' => 'Bielefeld – Gütersloh II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 132, 'name' => 'Herford – Minden-Lübbecke II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 133, 'name' => 'Minden-Lübbecke I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 134, 'name' => 'Lippe I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 135, 'name' => 'Höxter – Gütersloh III – Lippe II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 136, 'name' => 'Paderborn', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 137, 'name' => 'Hagen – Ennepe-Ruhr-Kreis I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 138, 'name' => 'Ennepe-Ruhr-Kreis II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 139, 'name' => 'Bochum I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 140, 'name' => 'Herne – Bochum II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 141, 'name' => 'Dortmund I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 142, 'name' => 'Dortmund II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 143, 'name' => 'Unna I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 144, 'name' => 'Hamm – Unna II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 145, 'name' => 'Soest', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 146, 'name' => 'Hochsauerlandkreis', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 147, 'name' => 'Siegen-Wittgenstein', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 148, 'name' => 'Olpe – Märkischer Kreis I', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 149, 'name' => 'Märkischer Kreis II', 'state' => 'Nordrhein-Westfalen'],
        ['number' => 150, 'name' => 'Nordsachsen', 'state' => 'Sachsen'],
        ['number' => 151, 'name' => 'Leipzig I', 'state' => 'Sachsen'],
        ['number' => 152, 'name' => 'Leipzig II', 'state' => 'Sachsen'],
        ['number' => 153, 'name' => 'Leipzig-Land', 'state' => 'Sachsen'],
        ['number' => 154, 'name' => 'Meißen', 'state' => 'Sachsen'],
        ['number' => 155, 'name' => 'Bautzen I', 'state' => 'Sachsen'],
        ['number' => 156, 'name' => 'Görlitz', 'state' => 'Sachsen'],
        ['number' => 157, 'name' => 'Sächsische Schweiz-Osterzgebirge', 'state' => 'Sachsen'],
        ['number' => 158, 'name' => 'Dresden I', 'state' => 'Sachsen'],
        ['number' => 159, 'name' => 'Dresden II – Bautzen II', 'state' => 'Sachsen'],
        ['number' => 160, 'name' => 'Mittelsachsen', 'state' => 'Sachsen'],
        ['number' => 161, 'name' => 'Chemnitz', 'state' => 'Sachsen'],
        ['number' => 162, 'name' => 'Chemnitzer Umland – Erzgebirgskreis II', 'state' => 'Sachsen'],
        ['number' => 163, 'name' => 'Erzgebirgskreis I', 'state' => 'Sachsen'],
        ['number' => 164, 'name' => 'Zwickau', 'state' => 'Sachsen'],
        ['number' => 165, 'name' => 'Vogtlandkreis', 'state' => 'Sachsen'],
        ['number' => 166, 'name' => 'Waldeck', 'state' => 'Hessen'],
        ['number' => 167, 'name' => 'Kassel', 'state' => 'Hessen'],
        ['number' => 168, 'name' => 'Werra-Meißner – Hersfeld-Rotenburg', 'state' => 'Hessen'],
        ['number' => 169, 'name' => 'Schwalm-Eder', 'state' => 'Hessen'],
        ['number' => 170, 'name' => 'Marburg', 'state' => 'Hessen'],
        ['number' => 171, 'name' => 'Lahn-Dill', 'state' => 'Hessen'],
        ['number' => 172, 'name' => 'Gießen', 'state' => 'Hessen'],
        ['number' => 173, 'name' => 'Fulda', 'state' => 'Hessen'],
        ['number' => 174, 'name' => 'Main-Kinzig – Wetterau II – Schotten', 'state' => 'Hessen'],
        ['number' => 175, 'name' => 'Hochtaunus', 'state' => 'Hessen'],
        ['number' => 176, 'name' => 'Wetterau I', 'state' => 'Hessen'],
        ['number' => 177, 'name' => 'Rheingau-Taunus – Limburg', 'state' => 'Hessen'],
        ['number' => 178, 'name' => 'Wiesbaden', 'state' => 'Hessen'],
        ['number' => 179, 'name' => 'Hanau', 'state' => 'Hessen'],
        ['number' => 180, 'name' => 'Main-Taunus', 'state' => 'Hessen'],
        ['number' => 181, 'name' => 'Frankfurt am Main I', 'state' => 'Hessen'],
        ['number' => 182, 'name' => 'Frankfurt am Main II', 'state' => 'Hessen'],
        ['number' => 183, 'name' => 'Groß-Gerau', 'state' => 'Hessen'],
        ['number' => 184, 'name' => 'Offenbach', 'state' => 'Hessen'],
        ['number' => 185, 'name' => 'Darmstadt', 'state' => 'Hessen'],
        ['number' => 186, 'name' => 'Odenwald', 'state' => 'Hessen'],
        ['number' => 187, 'name' => 'Bergstraße', 'state' => 'Hessen'],
        ['number' => 188, 'name' => 'Eichsfeld – Nordhausen – Kyffhäuserkreis', 'state' => 'Thüringen'],
        ['number' => 189, 'name' => 'Eisenach – Wartburgkreis – Unstrut-Hainich-Kreis', 'state' => 'Thüringen'],
        ['number' => 190, 'name' => 'Jena – Sömmerda – Weimarer Land I', 'state' => 'Thüringen'],
        ['number' => 191, 'name' => 'Gotha – Ilm-Kreis', 'state' => 'Thüringen'],
        ['number' => 192, 'name' => 'Erfurt – Weimar – Weimarer Land II', 'state' => 'Thüringen'],
        ['number' => 193, 'name' => 'Gera – Greiz – Altenburger Land', 'state' => 'Thüringen'],
        ['number' => 194, 'name' => 'Saalfeld-Rudolstadt – Saale-Holzland-Kreis – Saale-Orla-Kreis', 'state' => 'Thüringen'],
        ['number' => 195, 'name' => 'Suhl – Schmalkalden-Meiningen – Hildburghausen – Sonneberg', 'state' => 'Thüringen'],
        ['number' => 196, 'name' => 'Neuwied', 'state' => 'Rheinland-Pfalz'],
        ['number' => 197, 'name' => 'Ahrweiler', 'state' => 'Rheinland-Pfalz'],
        ['number' => 198, 'name' => 'Koblenz', 'state' => 'Rheinland-Pfalz'],
        ['number' => 199, 'name' => 'Mosel/Rhein-Hunsrück', 'state' => 'Rheinland-Pfalz'],
        ['number' => 200, 'name' => 'Kreuznach', 'state' => 'Rheinland-Pfalz'],
        ['number' => 201, 'name' => 'Bitburg', 'state' => 'Rheinland-Pfalz'],
        ['number' => 202, 'name' => 'Trier', 'state' => 'Rheinland-Pfalz'],
        ['number' => 203, 'name' => 'Montabaur', 'state' => 'Rheinland-Pfalz'],
        ['number' => 204, 'name' => 'Mainz', 'state' => 'Rheinland-Pfalz'],
        ['number' => 205, 'name' => 'Worms', 'state' => 'Rheinland-Pfalz'],
        ['number' => 206, 'name' => 'Ludwigshafen/Frankenthal', 'state' => 'Rheinland-Pfalz'],
        ['number' => 207, 'name' => 'Neustadt – Speyer', 'state' => 'Rheinland-Pfalz'],
        ['number' => 208, 'name' => 'Kaiserslautern', 'state' => 'Rheinland-Pfalz'],
        ['number' => 209, 'name' => 'Pirmasens', 'state' => 'Rheinland-Pfalz'],
        ['number' => 210, 'name' => 'Südpfalz', 'state' => 'Rheinland-Pfalz'],
        ['number' => 211, 'name' => 'Altötting', 'state' => 'Bayern'],
        ['number' => 212, 'name' => 'Erding – Ebersberg', 'state' => 'Bayern'],
        ['number' => 213, 'name' => 'Freising', 'state' => 'Bayern'],
        ['number' => 214, 'name' => 'Fürstenfeldbruck', 'state' => 'Bayern'],
        ['number' => 215, 'name' => 'Ingolstadt', 'state' => 'Bayern'],
        ['number' => 216, 'name' => 'München-Nord', 'state' => 'Bayern'],
        ['number' => 217, 'name' => 'München-Ost', 'state' => 'Bayern'],
        ['number' => 218, 'name' => 'München-Süd', 'state' => 'Bayern'],
        ['number' => 219, 'name' => 'München-West/Mitte', 'state' => 'Bayern'],
        ['number' => 220, 'name' => 'München-Land', 'state' => 'Bayern'],
        ['number' => 221, 'name' => 'Rosenheim', 'state' => 'Bayern'],
        ['number' => 222, 'name' => 'Bad Tölz-Wolfratshausen – Miesbach', 'state' => 'Bayern'],
        ['number' => 223, 'name' => 'Starnberg – Landsberg am Lech', 'state' => 'Bayern'],
        ['number' => 224, 'name' => 'Traunstein', 'state' => 'Bayern'],
        ['number' => 225, 'name' => 'Weilheim', 'state' => 'Bayern'],
        ['number' => 226, 'name' => 'Deggendorf', 'state' => 'Bayern'],
        ['number' => 227, 'name' => 'Landshut', 'state' => 'Bayern'],
        ['number' => 228, 'name' => 'Passau', 'state' => 'Bayern'],
        ['number' => 229, 'name' => 'Rottal-Inn', 'state' => 'Bayern'],
        ['number' => 230, 'name' => 'Straubing', 'state' => 'Bayern'],
        ['number' => 231, 'name' => 'Amberg', 'state' => 'Bayern'],
        ['number' => 232, 'name' => 'Regensburg', 'state' => 'Bayern'],
        ['number' => 233, 'name' => 'Schwandorf', 'state' => 'Bayern'],
        ['number' => 234, 'name' => 'Weiden', 'state' => 'Bayern'],
        ['number' => 235, 'name' => 'Bamberg', 'state' => 'Bayern'],
        ['number' => 236, 'name' => 'Bayreuth', 'state' => 'Bayern'],
        ['number' => 237, 'name' => 'Coburg', 'state' => 'Bayern'],
        ['number' => 238, 'name' => 'Hof', 'state' => 'Bayern'],
        ['number' => 239, 'name' => 'Kulmbach', 'state' => 'Bayern'],
        ['number' => 240, 'name' => 'Ansbach', 'state' => 'Bayern'],
        ['number' => 241, 'name' => 'Erlangen', 'state' => 'Bayern'],
        ['number' => 242, 'name' => 'Fürth', 'state' => 'Bayern'],
        ['number' => 243, 'name' => 'Nürnberg-Nord', 'state' => 'Bayern'],
        ['number' => 244, 'name' => 'Nürnberg-Süd', 'state' => 'Bayern'],
        ['number' => 245, 'name' => 'Roth', 'state' => 'Bayern'],
        ['number' => 246, 'name' => 'Aschaffenburg', 'state' => 'Bayern'],
        ['number' => 247, 'name' => 'Bad Kissingen', 'state' => 'Bayern'],
        ['number' => 248, 'name' => 'Main-Spessart', 'state' => 'Bayern'],
        ['number' => 249, 'name' => 'Schweinfurt', 'state' => 'Bayern'],
        ['number' => 250, 'name' => 'Würzburg', 'state' => 'Bayern'],
        ['number' => 251, 'name' => 'Augsburg-Stadt', 'state' => 'Bayern'],
        ['number' => 252, 'name' => 'Augsburg-Land', 'state' => 'Bayern'],
        ['number' => 253, 'name' => 'Donau-Ries', 'state' => 'Bayern'],
        ['number' => 254, 'name' => 'Neu-Ulm', 'state' => 'Bayern'],
        ['number' => 255, 'name' => 'Memmingen – Unterallgäu', 'state' => 'Bayern'],
        ['number' => 256, 'name' => 'Oberallgäu', 'state' => 'Bayern'],
        ['number' => 257, 'name' => 'Ostallgäu', 'state' => 'Bayern'],
        ['number' => 258, 'name' => 'Stuttgart I', 'state' => 'Baden-Württemberg'],
        ['number' => 259, 'name' => 'Stuttgart II', 'state' => 'Baden-Württemberg'],
        ['number' => 260, 'name' => 'Böblingen', 'state' => 'Baden-Württemberg'],
        ['number' => 261, 'name' => 'Esslingen', 'state' => 'Baden-Württemberg'],
        ['number' => 262, 'name' => 'Nürtingen', 'state' => 'Baden-Württemberg'],
        ['number' => 263, 'name' => 'Göppingen', 'state' => 'Baden-Württemberg'],
        ['number' => 264, 'name' => 'Waiblingen', 'state' => 'Baden-Württemberg'],
        ['number' => 265, 'name' => 'Ludwigsburg', 'state' => 'Baden-Württemberg'],
        ['number' => 266, 'name' => 'Neckar-Zaber', 'state' => 'Baden-Württemberg'],
        ['number' => 267, 'name' => 'Heilbronn', 'state' => 'Baden-Württemberg'],
        ['number' => 268, 'name' => 'Schwäbisch Hall – Hohenlohe', 'state' => 'Baden-Württemberg'],
        ['number' => 269, 'name' => 'Backnang – Schwäbisch Gmünd', 'state' => 'Baden-Württemberg'],
        ['number' => 270, 'name' => 'Aalen – Heidenheim', 'state' => 'Baden-Württemberg'],
        ['number' => 271, 'name' => 'Karlsruhe-Stadt', 'state' => 'Baden-Württemberg'],
        ['number' => 272, 'name' => 'Karlsruhe-Land', 'state' => 'Baden-Württemberg'],
        ['number' => 273, 'name' => 'Rastatt', 'state' => 'Baden-Württemberg'],
        ['number' => 274, 'name' => 'Heidelberg', 'state' => 'Baden-Württemberg'],
        ['number' => 275, 'name' => 'Mannheim', 'state' => 'Baden-Württemberg'],
        ['number' => 276, 'name' => 'Odenwald – Tauber', 'state' => 'Baden-Württemberg'],
        ['number' => 277, 'name' => 'Rhein-Neckar', 'state' => 'Baden-Württemberg'],
        ['number' => 278, 'name' => 'Bruchsal – Schwetzingen', 'state' => 'Baden-Württemberg'],
        ['number' => 279, 'name' => 'Pforzheim', 'state' => 'Baden-Württemberg'],
        ['number' => 280, 'name' => 'Calw', 'state' => 'Baden-Württemberg'],
        ['number' => 281, 'name' => 'Freiburg', 'state' => 'Baden-Württemberg'],
        ['number' => 282, 'name' => 'Lörrach – Müllheim', 'state' => 'Baden-Württemberg'],
        ['number' => 283, 'name' => 'Emmendingen – Lahr', 'state' => 'Baden-Württemberg'],
        ['number' => 284, 'name' => 'Offenburg', 'state' => 'Baden-Württemberg'],
        ['number' => 285, 'name' => 'Rottweil – Tuttlingen', 'state' => 'Baden-Württemberg'],
        ['number' => 286, 'name' => 'Schwarzwald-Baar', 'state' => 'Baden-Württemberg'],
        ['number' => 287, 'name' => 'Konstanz', 'state' => 'Baden-Württemberg'],
        ['number' => 288, 'name' => 'Waldshut', 'state' => 'Baden-Württemberg'],
        ['number' => 289, 'name' => 'Reutlingen', 'state' => 'Baden-Württemberg'],
        ['number' => 290, 'name' => 'Tübingen', 'state' => 'Baden-Württemberg'],
        ['number' => 291, 'name' => 'Ulm', 'state' => 'Baden-Württemberg'],
        ['number' => 292, 'name' => 'Biberach', 'state' => 'Baden-Württemberg'],
        ['number' => 293, 'name' => 'Bodensee', 'state' => 'Baden-Württemberg'],
        ['number' => 294, 'name' => 'Ravensburg', 'state' => 'Baden-Württemberg'],
        ['number' => 295, 'name' => 'Zollernalb – Sigmaringen', 'state' => 'Baden-Württemberg'],
        ['number' => 296, 'name' => 'Saarbrücken', 'state' => 'Saarland'],
        ['number' => 297, 'name' => 'Saarlouis', 'state' => 'Saarland'],
        ['number' => 298, 'name' => 'St. Wendel', 'state' => 'Saarland'],
        ['number' => 299, 'name' => 'Homburg', 'state' => 'Saarland'],
    ];

    public static function build(
        ?int $number = null,
        ?string $name = null,
        ?Ulid $id = new Ulid(),
    ): Wahlkreis {
        if (null === $number && null === $name) {
            $wahlkreisData = self::WAHLKREISE[array_rand(self::WAHLKREISE)];
        } else {
            $wahlkreisData = [];
            $wahlkreisData['number'] = $number ?? random_int(100, 200);
            $wahlkreisData['name'] = $name ?? 'Musterstadt I';
        }
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setNumber($wahlkreisData['number']);
        $wahlkreis->setName($wahlkreisData['name']);
        $wahlkreis->setId($id);
        $wahlkreis->setState(array_rand(Wahlkreise::UU_NUMBERS));
        $wahlkreis->setType(WahlkreisType::BTW);
        $wahlkreis->setYear(2025);

        return $wahlkreis;
    }
}
