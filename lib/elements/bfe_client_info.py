## $Id$
##
## This file is part of CDS Invenio.
## Copyright (C) 2002, 2003, 2004, 2005, 2006 CERN.
##
## CDS Invenio is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License as
## published by the Free Software Foundation; either version 2 of the
## License, or (at your option) any later version.
##
## CDS Invenio is distributed in the hope that it will be useful, but
## WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## General Public License for more details.  
##
## You should have received a copy of the GNU General Public License
## along with CDS Invenio; if not, write to the Free Software Foundation, Inc.,
## 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
"""BibFormat element - Prints client info
"""
__revision__ = "$Id$"

def format(bfo, var=''):
    '''
    Print several client specific variables.
    @param var the name of the desired variable. Can be one of: ln, search_pattern, uid
           lang: the current language of the user
           search_pattern: the list of keywords used by the user
           uid: the current user id
    '''
    
    if var == '':
        out =  ''
    elif var == 'ln':
        out = bfo.lang
    elif var == 'search_pattern':
        out = ' '.join(bfo.search_pattern)
    elif var == 'uid':
        out = bfo.uid
    else:
        out = 'Unknown variable: %s' % (var)
        
    return out