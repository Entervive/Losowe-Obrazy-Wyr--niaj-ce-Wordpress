# Losowe Obrazy Wyróżniające - WordPress Plugin

[![GitHub release (latest by date)](https://img.shields.io/github/v/release/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress?style=flat-square)](https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress/releases)
[![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue?style=flat-square&logo=wordpress)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple?style=flat-square&logo=php)](https://php.net/)
[![License](https://img.shields.io/github/license/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress?style=flat-square)](LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress?style=flat-square)](https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress/issues)

Plugin WordPress do automatycznego ustawiania losowych obrazów wyróżniających dla postów, które ich nie posiadają.

## 📋 Opis

Plugin "Losowe Obrazy Wyróżniające" pozwala na automatyczne przypisywanie losowych obrazów wyróżniających do postów w WordPress. Jest szczególnie przydatny dla blogów i stron, które publikują dużo treści i chcą zapewnić, że każdy post ma atrakcyjny obraz wyróżniający.

## ✨ Funkcjonalności

- 🖼️ **Wybór obrazów z biblioteki mediów** - Łatwy interfejs do wybierania obrazów
- 🎲 **Automatyczne losowe przypisywanie** - Plugin automatycznie ustawia losowy obraz przy publikacji nowych postów
- 🔄 **Zastosowanie do istniejących postów** - Możliwość jednorazowego dodania obrazów do wszystkich postów bez obrazów wyróżniających
- 🇵🇱 **Interfejs w języku polskim** - Kompletnie spolszczony interfejs
- 🛡️ **Bezpieczeństwo** - Zabezpieczenia przed nieautoryzowanym dostępem
- ⚙️ **Łatwe zarządzanie** - Intuicyjny panel administracyjny

## 🚀 Instalacja

### Opcja 1: Pobierz z Releases (Zalecane)

1. Przejdź do [Releases](https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress/releases)
2. Pobierz najnowszy plik ZIP
3. W panelu WordPress przejdź do **Wtyczki** → **Dodaj nową** → **Wgraj wtyczkę**
4. Wybierz pobrany plik ZIP i kliknij **"Zainstaluj teraz"**
5. Aktywuj plugin

### Opcja 2: Ręczna instalacja

1. Pobierz pliki pluginu
2. Stwórz folder `losowe-obrazy-wyrozniajace` w katalogu `/wp-content/plugins/`
3. Wgraj plik `losowe-obrazy-wyrozniajace.php` do utworzonego folderu
4. Przejdź do panelu administracyjnego WordPress
5. Aktywuj plugin w sekcji "Wtyczki"

### Opcja 3: Przez GitHub

```bash
cd /wp-content/plugins/
mkdir losowe-obrazy-wyrozniajace && cd losowe-obrazy-wyrozniajace
git clone https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress.git
```

## 📥 Szybkie pobieranie

[![Download Latest Release](https://img.shields.io/badge/Download-Latest%20Release-success?style=for-the-badge&logo=download)](https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress/releases/download/v1.0.1/losowe-obrazy-wyrozniajace.zip)

## 🔧 Konfiguracja

1. Po aktywacji przejdź do **Ustawienia** → **Losowe Obrazy Wyróżniające**
2. Kliknij przycisk **"Wybierz obrazy"**
3. Wybierz obrazy z biblioteki mediów WordPress (możesz wybrać wiele obrazów)
4. Kliknij **"Zapisz obrazy"**
5. Plugin jest gotowy do użycia!

## 📖 Jak używać

### Automatyczne działanie

Plugin automatycznie ustawi losowy obraz wyróżniający dla każdego nowego posta, który nie ma jeszcze ustawionego obrazu wyróżniającego.

### Zastosowanie do istniejących postów

Możesz jednorazowo dodać losowe obrazy wyróżniające do wszystkich istniejących postów:

1. Przejdź do ustawień pluginu
2. Kliknij **"Zastosuj do istniejących postów"**
3. Potwierdź akcję
4. Plugin automatycznie doda losowe obrazy do wszystkich postów bez obrazów wyróżniających

### Zarządzanie obrazami

- **Dodawanie obrazów**: Użyj przycisku "Wybierz obrazy" i wybierz dodatkowe obrazy
- **Usuwanie obrazów**: Kliknij przycisk "Usuń" pod każdym obrazem w podglądzie

## 🛠️ Wymagania

- WordPress 5.0 lub nowszy
- PHP 7.4 lub nowszy
- Uprawnienia administratora do konfiguracji

## 📱 Zrzuty ekranu

_Panel administracyjny z wybranymi obrazami_
_Interface wyboru obrazów_
_Potwierdzenie zastosowania do istniejących postów_

## 🐛 Zgłaszanie błędów

Jeśli napotkasz jakiekolwiek problemy, prosimy o zgłoszenie ich w sekcji [Issues](https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress/issues).

Przy zgłaszaniu błędu prosimy o podanie:

- Wersji WordPress
- Wersji PHP
- Opisu problemu
- Kroków do odtworzenia błędu

## 🤝 Współpraca

Zapraszamy do współpracy! Jeśli chcesz przyczynić się do rozwoju pluginu:

1. Zrób fork repozytorium
2. Stwórz branch dla swojej funkcjonalności (`git checkout -b nowa-funkcjonalnosc`)
3. Zatwierdź zmiany (`git commit -am 'Dodanie nowej funkcjonalności'`)
4. Wypchnij do brancha (`git push origin nowa-funkcjonalnosc`)
5. Stwórz Pull Request

## 📜 Licencja

Ten plugin jest udostępniony na licencji MIT. Zobacz plik [LICENSE](LICENSE) po więcej szczegółów.

## 👨‍💻 Autor

**Aleksander Staszków (Entervive)**

- Strona: [entervive.pl](https://entervive.pl)
- GitHub: [@entervive](https://github.com/entervive)

## 📝 Changelog

### 1.0.1 (25.07.2025)

- Zapewnienie lepszego wyniku testów w Plugin Check (PCP)

### 1.0.0 (25.07.2025)

- Pierwsze wydanie
- Podstawowa funkcjonalność wyboru i przypisywania losowych obrazów
- Interfejs w języku polskim
- Automatyczne przypisywanie przy publikacji
- Zastosowanie do istniejących postów

---

⭐ Jeśli plugin Ci się podoba, zostaw gwiazdkę na GitHub!

🐞 Znalazłeś błąd? [Zgłoś go tutaj](https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress/issues)
