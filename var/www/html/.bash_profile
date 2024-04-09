# .bash_profile

# Get the aliases and functions
if [ -f ~/.bashrc ]; then
        . ~/.bashrc
fi

# User specific environment and startup programs

PS1="[\[\e[01;37m\]\u@Umbler\[\e[0m\] \[\e[01;34m\]\W\[\e[0m\]]$ "

PATH=$PATH:$HOME/bin:/usr/local/php/PHP73/bin

export PS1
export PATH
