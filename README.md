# Palasis_Inheritwidgets

This makes themes inherit widgets from their parents

for Magento 1.9x

Original post of the issue on Stackexchange (by me):  [Inherit Widgets from Theme Parent](https://magento.stackexchange.com/questions/221255/inherit-widgets-from-theme-parent)

The purpose of this module is to allow child themes to inherit widgets from their parents. 

Widget sort order accross multple themes is not obeyed.

For sorting to be obeyed, put widgets in only one of the parent themes and nowhere else.

example:

    rwd / default
    \_ rwd / palasis <-- all widgets live here
       \_ rwd / palasis-green
       \_ rwd / palasis-*othercolors*...
       \_ rwd / palasis-xmas
       \_ rwd / palasis-summer

all child themes must specify their parentage in theme.xml

eg. for `rwd/palasis-green` put xml file at: `design/frontend/rwd/palasis-green/etc/theme.xml`

```
<?xml version="1.0"?>
<theme>
    <parent>rwd/palasis</parent>
</theme>
```
