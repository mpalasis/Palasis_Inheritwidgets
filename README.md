# Palasis_Inheritwidgets

This makes themes inherit widgets from their parents

for Magento 1.9x

This makes child themes inherit widgets from their parents

Original post of the issue on Stackexchange (by me):  [Inherit Widgets from Theme Parent](https://magento.stackexchange.com/questions/221255/inherit-widgets-from-theme-parent)


Sorting can be iffy because it's per-theme.
The purpose of this module is to allow child themes to inherit widgets from their parents.
For sorting to be obeyed, put widgets in only one of the parent themes and nowhere else.

example:

    rwd / default
    \_ rwd / palasis <-- all widgets live here
       \_ rwd / palasis-green
       \_ rwd / palasis-*othercolors*...
       \_ rwd / palasis-xmas
       \_ rwd / palasis-summer

