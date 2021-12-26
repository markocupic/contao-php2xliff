<p align="center"><a href="https://github.com/markocupic"><img src="docs/logo.png" width="200"></a></p>

# Generate XLIFF translation files from PHP array translation files
This little handy Contao backend extension simply converts contao php translation files into their xliff (*.xlf) pendants.
 The newly generated files will be stored side by side to the already existing php files.
 Already existing *.xlf files will be overwritten.

![Backend](docs/screenshot_backend_2.png)
![Backend](docs/screenshot_backend_1.png)

# Changing the source language
The source language for all Contao Core XLIFF files is english (en).
 If you like to change this setting you have to do that in your project config in `config/config.yml`.

```yaml
# config/config.yml
markocupic_contao_php2xliff:
  sourceLanguage: it # Switch the source language to Italian
```
