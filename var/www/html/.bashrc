# .bashrc

# User specific aliases and functions
alias cp='cp -i'
alias ll='ls -l --color'
alias mv='mv -i'
alias rm='rm -i'
alias umbler='/usr/local/bin/umbler'
export PATH="$PATH:/usr/bin:/bin:/usr/sbin"

# Source global definitions
if [ -f /etc/bashrc ]; then
        . /etc/bashrc
fi
alias php='/usr/local/bin/php73' 
