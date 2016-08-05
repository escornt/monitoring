#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/stat.h>
#include <dirent.h>
#include <time.h>

char      *get_word(char *s, int pos)
{
  int     cmpt;
  char    *res;
  int     i;
  int     j;

  i = 0;
  j = 0;
  cmpt = 0;
  pos = pos - 1;
  if ((res = malloc(strlen(s) + 1)) == NULL)
    return (NULL);
  while (cmpt < pos)
    {
      while (s[i] != '\n' && s[i])
        i++;
      i++;
      cmpt++;
    }
  while (s[i] != '\n' && s[i])
    res[j++] = s[i++];
  res[j] = 0;
  return (res);
}

int       test_ct(char *line, char *id)
{
  long long     ct_time;
  long long     current_time;
  long long     res;
  FILE          *fd;
  char          *buf;

  ct_time = atoll(line);
  current_time = (long long)time(NULL);
  res = current_time - ct_time;
  if ((fd = popen("./getstatus", "r")) == -1)
    return (1);
  if ((buf = malloc(20)) == NULL)
    return (1);
  fread(buf, 1, 20, fd);
  if (strcmp("running", buf) == 0)
    if (res > 1296000)
      {

      }
  if (strcmp("stopped", buf) == 0)
    if (res > 2592000)
      {

      }
  pclose(fd);
}

int       handle_line(char *line, char *id)
{
  char    command;

  if (line == NULL)
    return (1);
  if (line[0] == 0)
    return (1);
  if (line[0] == 'e' && (line[1] == 0 || line[1] == '\n'))
    {
      command = strcat("vzctl stop ", id);
      system(command);
      return (0);
    }
  else if (line[0] == 'e' && (line[1] == 0 || line[1] == '\n'))
    {
      command = strcat("vzctl destroy ", id);
      system(command);
      return (0);
    }
  else if (line[0] == 'd' && (line[1] == 0 || line[1] == '\n'))
    {
      return (0);
    }
  test_ct(line, id);
}
