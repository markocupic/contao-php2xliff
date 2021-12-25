<p align="center"><a href="https://github.com/markocupic"><img src="docs/logo.png" width="200"></a></p>

# Welcome to Contao PHP language file to XLIFF
This little handy extension simply converts contao php translation files into their xliff (*.xlf) pendants.
 The newly generated files will be stored side by side to the already existing php files.
 Already existing *.xlf files will be overwritten.

# Changing the source language
The source language for all Contao Core XLIFF files is english (en).
 If you like to change this setting you have to do that in your project config in `config/config.yml`.

```yaml
# config/config.yml
markocupic_contao_php2xliff:
  sourceLanguage: it # Change the source language to it
```
