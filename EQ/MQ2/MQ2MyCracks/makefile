!include "../global.mak"

ALL : "$(OUTDIR)\MQ2MyCracks.dll"

CLEAN :
	-@erase "$(INTDIR)\MQ2MyCracks.obj"
	-@erase "$(INTDIR)\vc60.idb"
	-@erase "$(OUTDIR)\MQ2MyCracks.dll"
	-@erase "$(OUTDIR)\MQ2MyCracks.exp"
	-@erase "$(OUTDIR)\MQ2MyCracks.lib"
	-@erase "$(OUTDIR)\MQ2MyCracks.pdb"


LINK32=link.exe
LINK32_FLAGS=kernel32.lib user32.lib gdi32.lib winspool.lib comdlg32.lib advapi32.lib shell32.lib ole32.lib oleaut32.lib uuid.lib odbc32.lib odbccp32.lib $(DETLIB) ..\Release\MQ2Main.lib /nologo /dll /incremental:no /pdb:"$(OUTDIR)\MQ2MyCracks.pdb" /debug /machine:I386 /out:"$(OUTDIR)\MQ2MyCracks.dll" /implib:"$(OUTDIR)\MQ2MyCracks.lib" /OPT:NOICF /OPT:NOREF 
LINK32_OBJS= \
	"$(INTDIR)\MQ2MyCracks.obj" \
	"$(OUTDIR)\MQ2Main.lib"

"$(OUTDIR)\MQ2MyCracks.dll" : "$(OUTDIR)" $(DEF_FILE) $(LINK32_OBJS)
    $(LINK32) $(LINK32_FLAGS) $(LINK32_OBJS)


!IF "$(NO_EXTERNAL_DEPS)" != "1"
!IF EXISTS("MQ2MyCracks.dep")
!INCLUDE "MQ2MyCracks.dep"
!ELSE 
!MESSAGE Warning: cannot find "MQ2MyCracks.dep"
!ENDIF 
!ENDIF 


SOURCE=.\MQ2MyCracks.cpp

"$(INTDIR)\MQ2MyCracks.obj" : $(SOURCE) "$(INTDIR)"

