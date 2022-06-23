<!-- SHIELDS -->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![License][license-shield]][license-url]

<p>
  <a href="https://github.com/helsingborg-stad/builder">
    <img src="images/hbg-github-logo-combo.png" alt="Logo" width="300">
  </a>
</p>
<h1>Builder</h1>
<p>
  Composer builder plugin
  <br />
  <a href="https://github.com/helsingborg-stad/builder/issues">Report Bug</a>
  ·
  <a href="https://github.com/helsingborg-stad/builder/issues">Request Feature</a>
</p>

## Summary
This composer plugin will check for build and cleanup configuration on composer packages that is being installed from the same composer.json file.
The intended usage for this is in build pipeline and local development environment.

## Requirements
- [PHP 7.4 or higher](https://www.php.net/)

## Install
```bash
composer require helsingborg-stad/builder
```

## Build command
I any composer package that need to be built the below should be in composer.json file like this.
```json
{
    "extra": {
        "builder": {
            "commands": [
                "npm ci",
                "npm run build",
            ]
        }
    }
}
```

## Cleanup
Any files that should be cleaned up after build in required packages should be in the composer.json file like this.
```json
{
    "extra": {
        "builder": {
            "removables": [
                ".git",
                ".gitignore"
            ]
        }
    }
}
```

### Pipeline commands example
When running build for production the enable cleanup config should be enabled before install.  
This should BE USED WITH CAUTION as it will delete files from your system and the setting should not be commited but added during build in a pipeline.

```bash
composer config extra.builder.cleanup true
composer install
```
This will be added in the composer.json
```json
    "extra": {
        "builder": {
            "cleanup": "true"
        }
    }
```

### Run tests
```bash
composer install
composer run test
```

## Roadmap

See the [open issues][issues-url] for a list of proposed features (and known issues).



## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



## License

Distributed under the [MIT License][license-url].



## Acknowledgements

- [othneildrew Best README Template](https://github.com/othneildrew/Best-README-Template)



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/helsingborg-stad/builder.svg?style=flat-square
[contributors-url]: https://github.com/helsingborg-stad/builder/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/helsingborg-stad/builder.svg?style=flat-square
[forks-url]: https://github.com/helsingborg-stad/builder/network/members
[stars-shield]: https://img.shields.io/github/stars/helsingborg-stad/builder.svg?style=flat-square
[stars-url]: https://github.com/helsingborg-stad/builder/stargazers
[issues-shield]: https://img.shields.io/github/issues/helsingborg-stad/builder.svg?style=flat-square
[issues-url]: https://github.com/helsingborg-stad/builder/issues
[license-shield]: https://img.shields.io/github/license/helsingborg-stad/builder.svg?style=flat-square
[license-url]: https://raw.githubusercontent.com/helsingborg-stad/builder/master/LICENSE
